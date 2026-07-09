<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pupuk', 'description' => 'Segala jenis pupuk tanaman'],
            ['name' => 'Benih', 'description' => 'Benih atau bibit tanaman unggul'],
            ['name' => 'Pestisida', 'description' => 'Cairan/racun pembasmi hama tanaman'],
            ['name' => 'Alat', 'description' => 'Alat-alat pertanian dan kelengkapannya'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
