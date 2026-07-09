<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupToTelegram extends Command
{
    protected $signature = 'backup:telegram';

    protected $description = 'Backup database dan kirim ke Telegram';

    public function handle(): int
    {
        $this->info('Memulai backup database...');

        try {
            // 1. Get DB credentials from config
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $port = config('database.connections.mysql.port', '3306');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            if (! $database) {
                $this->error('Database name not configured.');

                return self::FAILURE;
            }

            // 2. Create backup directory
            $backupDir = storage_path('app/backups');
            if (! is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d-His');
            $sqlFile = "{$backupDir}/backup-{$timestamp}.sql";
            $gzFile = "{$sqlFile}.gz";

            // 3. Run mysqldump
            $dumpCommand = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database)
            );

            $result = Process::run("{$dumpCommand} > \"{$sqlFile}\"");

            if (! file_exists($sqlFile) || filesize($sqlFile) === 0) {
                $this->error('mysqldump failed or produced empty file.');
                Log::error('Backup failed: mysqldump produced empty file.', [
                    'stderr' => $result->errorOutput(),
                ]);

                return self::FAILURE;
            }

            $this->info("SQL dump created: {$sqlFile} (" . $this->formatBytes(filesize($sqlFile)) . ')');

            // 4. Gzip the file
            $gzContent = gzencode(file_get_contents($sqlFile), 9);
            file_put_contents($gzFile, $gzContent);
            unlink($sqlFile); // Remove uncompressed SQL

            $this->info("Compressed: {$gzFile} (" . $this->formatBytes(filesize($gzFile)) . ')');

            // 5. Send to Telegram
            $botToken = $this->getTelegramConfig('bot_token');
            $chatId = $this->getTelegramConfig('chat_id');

            if ($botToken && $chatId) {
                $this->sendToTelegram($botToken, $chatId, $gzFile, $database, $timestamp);
            } else {
                $this->warn('Telegram bot token atau chat ID belum dikonfigurasi. Backup hanya disimpan lokal.');
            }

            // 6. Cleanup: keep only last 3 local backups
            $this->cleanupOldBackups($backupDir, 3, 7);

            $this->info('Backup selesai.');
            Log::info("Database backup completed: {$gzFile}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Backup gagal: {$e->getMessage()}");
            Log::error('Database backup failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Send the backup file to Telegram via Bot API.
     */
    private function sendToTelegram(string $botToken, string $chatId, string $filePath, string $database, string $timestamp): void
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendDocument";

        $caption = "🗄️ *Database Backup*\n"
            . "📅 {$timestamp}\n"
            . "💾 Database: {$database}\n"
            . "📦 Size: " . $this->formatBytes(filesize($filePath));

        $response = Http::timeout(120)
            ->attach('document', file_get_contents($filePath), basename($filePath))
            ->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

        if ($response->successful() && $response->json('ok')) {
            $this->info('Backup berhasil dikirim ke Telegram.');
        } else {
            $this->warn('Gagal mengirim ke Telegram: ' . $response->body());
            Log::warning('Telegram backup upload failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Remove old backup files. Keep only the latest N files and delete anything older than maxDays.
     */
    private function cleanupOldBackups(string $directory, int $keepCount, int $maxDays): void
    {
        $files = glob("{$directory}/backup-*.sql.gz");

        if (! $files) {
            return;
        }

        // Sort by modification time, newest first
        usort($files, fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

        $cutoffTime = now()->subDays($maxDays)->getTimestamp();
        $deleted = 0;

        foreach ($files as $index => $file) {
            $shouldDelete = $index >= $keepCount || filemtime($file) < $cutoffTime;

            if ($shouldDelete) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup file(s).");
        }
    }

    /**
     * Get Telegram configuration from settings table or env.
     */
    private function getTelegramConfig(string $key): ?string
    {
        // Try settings table first, fall back to .env
        try {
            $value = \App\Models\Setting::get("telegram_{$key}");
            if ($value) {
                return $value;
            }
        } catch (\Throwable) {
            // Settings table may not exist yet
        }

        return match ($key) {
            'bot_token' => env('TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            default => null,
        };
    }

    /**
     * Format bytes to human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
