<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Karung', 'symbol' => 'karung'],
            ['name' => 'Liter', 'symbol' => 'liter'],
            ['name' => 'Botol', 'symbol' => 'botol'],
            ['name' => 'Ikat', 'symbol' => 'ikat'],
            ['name' => 'Pcs', 'symbol' => 'pcs'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
