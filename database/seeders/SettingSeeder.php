<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['key' => 'min_margin'],
            [
                'value' => '1000',
                'description' => 'Margin minimum per produk dalam rupiah. Jika margin harga jual kurang dari ini setelah ada transaksi pembelian baru, harga jual akan otomatis disesuaikan.'
            ]
        );
    }
}
