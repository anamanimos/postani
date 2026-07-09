<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class VerifyBusinessLogic extends Command
{
    protected $signature = 'verify:logic';
    protected $description = 'Verifikasi alur bisnis end-to-end (pembelian, margin, penjualan, konversi satuan)';

    public function handle(): int
    {
        $this->info('=== Memulai Verifikasi Alur Bisnis ===');

        try {
            DB::beginTransaction();

            // 1. Reset data untuk testing
            $this->info('Mempersiapkan data uji...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            PurchaseItem::truncate();
            Purchase::truncate();
            SaleItem::truncate();
            Sale::truncate();
            Product::truncate();
            Supplier::truncate();
            Customer::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Ambil Kategori & Satuan
            $pupukCat = Category::where('name', 'Pupuk')->first();
            $kgUnit = Unit::where('symbol', 'kg')->first();
            $karungUnit = Unit::where('symbol', 'karung')->first();

            if (!$pupukCat || !$kgUnit || !$karungUnit) {
                $this->error('Gagal: Pastikan seeder database sudah dijalankan!');
                DB::rollBack();
                return self::FAILURE;
            }

            // 2. Buat Produk Baru: Pupuk Urea (Satuan Beli: kg, Satuan Jual: karung, Konversi: 25)
            // Artinya: 1 karung = 25 kg.
            $this->info('Membuat produk baru: Pupuk Urea (Beli: kg, Jual: karung, Konversi: 25)...');
            $product = Product::create([
                'name' => 'Pupuk Urea Subisidi',
                'category_id' => $pupukCat->id,
                'buy_unit_id' => $kgUnit->id,
                'sell_unit_id' => $karungUnit->id,
                'conversion_factor' => 25.0000,
                'selling_price' => 120000.00, // Harga awal per karung
                'min_stock' => 10.00,
                'is_active' => true,
            ]);

            // 3. Buat Tengkulak & Pelanggan
            $supplier = Supplier::create(['name' => 'Tengkulak H. Anwar']);
            $customer = Customer::create(['name' => 'Petani Subur']);

            $this->info("Tengkulak: {$supplier->name} dibuat.");
            $this->info("Pelanggan: {$customer->name} dibuat.");

            // 4. Pembelian Pertama: Beli 100 kg Urea dengan harga Rp 4.000 / kg
            // Total belanja: Rp 400.000
            // Setelah pembelian, stok harus menjadi 100 kg.
            // HPP / last_purchase_price = Rp 4.000.
            // Biaya per karung = 4000 * 25 = Rp 100.000.
            // Margin = 120.000 - 100.000 = 20.000. min_margin=1000, jadi harga jual harus TETAP Rp 120.000.
            $this->info('--- TRANSAKSI 1: Pembelian Pertama (100 kg @ Rp 4.000/kg) ---');
            $purchase1 = Purchase::create([
                'invoice_number' => Purchase::generateInvoiceNumber(),
                'supplier_id' => $supplier->id,
                'purchase_date' => now(),
                'total_amount' => 400000.00,
                'payment_status' => 'paid',
                'paid_amount' => 400000.00,
                'due_amount' => 0.00,
                'created_by' => 1,
            ]);

            $purchaseItem1 = PurchaseItem::create([
                'purchase_id' => $purchase1->id,
                'product_id' => $product->id,
                'quantity' => 100.00,
                'unit_price' => 4000.00,
                'subtotal' => 400000.00,
            ]);

            $product->refresh();
            $this->info("Stok setelah beli: {$product->stock} kg (Ekspektasi: 100 kg)");
            $this->info("Harga beli terakhir: Rp " . number_format($product->last_purchase_price, 0));
            $this->info("Rata-rata harga beli: Rp " . number_format($product->avg_purchase_price, 0));
            $this->info("Harga jual: Rp " . number_format($product->selling_price, 0) . " (Ekspektasi: 120.000)");

            if ($product->stock != 100) throw new \Exception('Kesalahan stok pembelian 1!');

            // 5. Pembelian Kedua (Harga Naik): Beli 100 kg Urea dengan harga Rp 5.000 / kg
            // Total belanja: Rp 500.000.
            // Setelah pembelian:
            // Stok = 100 + 100 = 200 kg.
            // last_purchase_price = Rp 5.000.
            // avg_purchase_price = (100 * 4000 + 100 * 5000) / 200 = Rp 4.500.
            // Biaya per karung = 5000 * 25 = Rp 125.000.
            // Margin lama = 120.000 - 125.000 = -5.000 (Kurang dari min_margin = 1.000).
            // Harga jual baru otomatis diset ke = 125.000 + 1.000 = Rp 126.000.
            $this->info('--- TRANSAKSI 2: Pembelian Kedua dengan harga naik (100 kg @ Rp 5.000/kg) ---');
            $purchase2 = Purchase::create([
                'invoice_number' => Purchase::generateInvoiceNumber(),
                'supplier_id' => $supplier->id,
                'purchase_date' => now(),
                'total_amount' => 500000.00,
                'payment_status' => 'paid',
                'paid_amount' => 500000.00,
                'due_amount' => 0.00,
                'created_by' => 1,
            ]);

            $purchaseItem2 = PurchaseItem::create([
                'purchase_id' => $purchase2->id,
                'product_id' => $product->id,
                'quantity' => 100.00,
                'unit_price' => 5000.00,
                'subtotal' => 500000.00,
            ]);

            $product->refresh();
            $this->info("Stok setelah beli kedua: {$product->stock} kg (Ekspektasi: 200 kg)");
            $this->info("Harga beli terakhir: Rp " . number_format($product->last_purchase_price, 0) . " (Ekspektasi: 5.000)");
            $this->info("Rata-rata harga beli: Rp " . number_format($product->avg_purchase_price, 0) . " (Ekspektasi: 4.500)");
            $this->info("Harga jual otomatis naik: Rp " . number_format($product->selling_price, 0) . " (Ekspektasi: 126.000)");

            if ($product->stock != 200) throw new \Exception('Kesalahan stok pembelian 2!');
            if ($product->selling_price != 126000) throw new \Exception('Kesalahan auto-adjust harga jual!');

            // 6. Penjualan ke Pelanggan: Jual 2 Karung
            // 2 Karung = 2 * 25 = 50 kg.
            // Setelah penjualan, stok harus menjadi 200 - 50 = 150 kg.
            $this->info('--- TRANSAKSI 3: Penjualan ke Pelanggan (2 karung @ Rp 126.000/karung) ---');
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'sale_date' => now(),
                'total_amount' => 252000.00,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'paid_amount' => 252000.00,
                'due_amount' => 0.00,
                'created_by' => 1,
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => 2.00,
                'unit_price' => 126000.00,
                'subtotal' => 252000.00,
            ]);

            $product->refresh();
            $this->info("Stok setelah jual 2 karung (50 kg): {$product->stock} kg (Ekspektasi: 150 kg)");

            if ($product->stock != 150) throw new \Exception('Kesalahan pengurangan stok setelah penjualan!');

            // 7. Pengujian Validasi Stok (Jual melebihi stok)
            // Stok tersisa: 150 kg = 6 karung.
            // Coba jual 10 karung = 250 kg. Harus throw exception.
            $this->info('--- TRANSAKSI 4: Pengujian Validasi Stok (Jual melebihi stok, ekspektasi gagal) ---');
            $invalidSale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'sale_date' => now(),
                'total_amount' => 1260000.00,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'paid_amount' => 1260000.00,
                'due_amount' => 0.00,
                'created_by' => 1,
            ]);

            try {
                SaleItem::create([
                    'sale_id' => $invalidSale->id,
                    'product_id' => $product->id,
                    'quantity' => 10.00, // 250 kg
                    'unit_price' => 126000.00,
                    'subtotal' => 1260000.00,
                ]);
                $this->error('Gagal: Transaksi tidak mencukupi stok berhasil disimpan!');
                DB::rollBack();
                return self::FAILURE;
            } catch (\RuntimeException $e) {
                $this->info("Sukses: Pengurangan stok berhasil dicegah dengan pesan: " . $e->getMessage());
            }

            DB::rollBack();
            $this->info('=====================================');
            $this->info('VERIFIKASI ALUR BISNIS BERHASIL! (Semua uji lulus)');
            $this->info('=====================================');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Kesalahan: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
