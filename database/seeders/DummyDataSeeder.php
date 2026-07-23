<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Users exist
        $admin = User::firstOrCreate(
            ['email' => 'cranam21@gmail.com'],
            [
                'name' => 'Admin Cranam',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '081234567890'
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@postani.com'],
            [
                'name' => 'Budi Kasir',
                'password' => Hash::make('12345678'),
                'role' => 'kasir',
                'is_active' => true,
                'phone' => '089876543210'
            ]
        );

        // 2. Units
        $unitKg = Unit::firstOrCreate(['symbol' => 'kg'], ['name' => 'Kilogram', 'symbol' => 'kg']);
        $unitKarung = Unit::firstOrCreate(['symbol' => 'karung'], ['name' => 'Karung', 'symbol' => 'karung']);
        $unitLiter = Unit::firstOrCreate(['symbol' => 'liter'], ['name' => 'Liter', 'symbol' => 'liter']);
        $unitBotol = Unit::firstOrCreate(['symbol' => 'botol'], ['name' => 'Botol', 'symbol' => 'botol']);
        $unitPcs = Unit::firstOrCreate(['symbol' => 'pcs'], ['name' => 'Pcs', 'symbol' => 'pcs']);
        $unitPack = Unit::firstOrCreate(['symbol' => 'pack'], ['name' => 'Pack', 'symbol' => 'pack']);

        // 3. Categories
        $catPupuk = Category::firstOrCreate(['name' => 'Pupuk'], ['name' => 'Pupuk', 'description' => 'Segala jenis pupuk tanaman']);
        $catBenih = Category::firstOrCreate(['name' => 'Benih'], ['name' => 'Benih', 'description' => 'Benih & bibit tanaman unggul']);
        $catPestisida = Category::firstOrCreate(['name' => 'Pestisida'], ['name' => 'Pestisida', 'description' => 'Cairan pembasmi hama & gulma']);
        $catAlat = Category::firstOrCreate(['name' => 'Alat Pertanian'], ['name' => 'Alat Pertanian', 'description' => 'Peralatan perkebunan & kelengkapannya']);

        // 4. Suppliers
        $suppliers = [
            Supplier::firstOrCreate(['name' => 'PT Pupuk Indonesia (Persero)'], ['phone' => '021-5432100', 'address' => 'Jl. Taman Anggrek No. 12, Jakarta', 'notes' => 'Supplier pupuk resmi']),
            Supplier::firstOrCreate(['name' => 'CV Tani Makmur Sejahtera'], ['phone' => '0341-778899', 'address' => 'Jl. Raya Agro No. 45, Malang', 'notes' => 'Distributor racun & alat tani']),
            Supplier::firstOrCreate(['name' => 'PT Syngenta Indonesia'], ['phone' => '021-7890123', 'address' => 'Kawasan Industri Cikarang, Bekasi', 'notes' => 'Produsen benih & obat-obatan']),
            Supplier::firstOrCreate(['name' => 'PT BISI International Tbk'], ['phone' => '0354-667788', 'address' => 'Jl. Industri No. 8, Kediri', 'notes' => 'Supplier benih jagung & hortikultura']),
        ];

        // 5. Customers
        $customers = [
            Customer::firstOrCreate(['phone' => '081334455667'], ['name' => 'Pak Slamet (Kelompok Tani Subur)', 'address' => 'Desa Sumbersekar RT 02 RW 01']),
            Customer::firstOrCreate(['phone' => '085211223344'], ['name' => 'Ibu Sri Rahayu', 'address' => 'Dusun Krajan No. 14']),
            Customer::firstOrCreate(['phone' => '087855667788'], ['name' => 'Pak Bambang Utomo', 'address' => 'Jl. Sawah Indah No. 8']),
            Customer::firstOrCreate(['phone' => '089900112233'], ['name' => 'Pak Haji Mahmud', 'address' => 'Desa Tegalrejo']),
        ];

        // 6. Products (Stok & Conversion Factor diselaraskan)
        $productsData = [
            [
                'sku' => 'PRD-001',
                'name' => 'Pupuk Urea Subur 50kg',
                'category_id' => $catPupuk->id,
                'buy_unit_id' => $unitKarung->id,
                'sell_unit_id' => $unitKarung->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 150000,
                'avg_purchase_price' => 150000,
                'selling_price' => 165000,
                'stock' => 100, // 100 karung
                'min_stock' => 10,
                'notes' => 'Pupuk Nitrogen tinggi 46%'
            ],
            [
                'sku' => 'PRD-002',
                'name' => 'Pupuk NPK Phonska 15-15-15 50kg',
                'category_id' => $catPupuk->id,
                'buy_unit_id' => $unitKarung->id,
                'sell_unit_id' => $unitKarung->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 175000,
                'avg_purchase_price' => 175000,
                'selling_price' => 190000,
                'stock' => 80, // 80 karung
                'min_stock' => 10,
                'notes' => 'Pupuk Majemuk NPK'
            ],
            [
                'sku' => 'PRD-003',
                'name' => 'Benih Jagung Hibrida BISI-18 (1kg)',
                'category_id' => $catBenih->id,
                'buy_unit_id' => $unitPack->id,
                'sell_unit_id' => $unitPack->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 85000,
                'avg_purchase_price' => 85000,
                'selling_price' => 105000,
                'stock' => 50,
                'min_stock' => 5,
                'notes' => 'Benih jagung tahan tongkol ganda'
            ],
            [
                'sku' => 'PRD-004',
                'name' => 'Herbisida Roundup 486 SL (1 Liter)',
                'category_id' => $catPestisida->id,
                'buy_unit_id' => $unitBotol->id,
                'sell_unit_id' => $unitBotol->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 95000,
                'avg_purchase_price' => 95000,
                'selling_price' => 115000,
                'stock' => 40,
                'min_stock' => 5,
                'notes' => 'Pembasmi rumput sistemik'
            ],
            [
                'sku' => 'PRD-005',
                'name' => 'Insektisida Gramoxone 276 SL (1 Liter)',
                'category_id' => $catPestisida->id,
                'buy_unit_id' => $unitBotol->id,
                'sell_unit_id' => $unitBotol->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 70000,
                'avg_purchase_price' => 70000,
                'selling_price' => 88000,
                'stock' => 35,
                'min_stock' => 5,
                'notes' => 'Herbisida kontak pembakar rumput'
            ],
            [
                'sku' => 'PRD-006',
                'name' => 'Tangki Semprot Elektrik DGW 16 Liter',
                'category_id' => $catAlat->id,
                'buy_unit_id' => $unitPcs->id,
                'sell_unit_id' => $unitPcs->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 420000,
                'avg_purchase_price' => 420000,
                'selling_price' => 520000,
                'stock' => 12,
                'min_stock' => 2,
                'notes' => 'Tangki sprayer baterai rechargeable'
            ],
            [
                'sku' => 'PRD-007',
                'name' => 'Cangkul Baja Super Cap Buaya',
                'category_id' => $catAlat->id,
                'buy_unit_id' => $unitPcs->id,
                'sell_unit_id' => $unitPcs->id,
                'conversion_factor' => 1,
                'last_purchase_price' => 65000,
                'avg_purchase_price' => 65000,
                'selling_price' => 85000,
                'stock' => 20,
                'min_stock' => 3,
                'notes' => 'Cangkul baja kuat tahan karat'
            ],
        ];

        $products = [];
        foreach ($productsData as $pData) {
            $products[] = Product::firstOrCreate(['sku' => $pData['sku']], $pData);
        }

        // 7. Purchases (Pembelian Barang Masuk dari Supplier)
        if (Purchase::where('invoice_number', 'PB-202607-001')->doesntExist()) {
            $p1Total = (10 * 150000) + (10 * 175000); // 3,250,000
            $pur1 = Purchase::create([
                'invoice_number' => 'PB-202607-001',
                'supplier_id' => $suppliers[0]->id,
                'purchase_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'total_amount' => $p1Total,
                'payment_status' => 'paid',
                'paid_amount' => $p1Total,
                'due_amount' => 0,
                'notes' => 'Stok awal pupuk bulanan',
                'created_by' => $admin->id,
            ]);

            PurchaseItem::create([
                'purchase_id' => $pur1->id,
                'product_id' => $products[0]->id,
                'quantity' => 10,
                'unit_price' => 150000,
                'subtotal' => 1500000,
            ]);

            PurchaseItem::create([
                'purchase_id' => $pur1->id,
                'product_id' => $products[1]->id,
                'quantity' => 10,
                'unit_price' => 175000,
                'subtotal' => 1750000,
            ]);
        }

        if (Purchase::where('invoice_number', 'PB-202607-002')->doesntExist()) {
            $p2Total = (20 * 95000) + (5 * 420000); // 1,900,000 + 2,100,000 = 4,000,000
            $pur2 = Purchase::create([
                'invoice_number' => 'PB-202607-002',
                'supplier_id' => $suppliers[1]->id,
                'purchase_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'total_amount' => $p2Total,
                'payment_status' => 'partial',
                'paid_amount' => 2500000,
                'due_amount' => 1500000,
                'notes' => 'Pembelian alat & pestisida',
                'created_by' => $admin->id,
            ]);

            PurchaseItem::create([
                'purchase_id' => $pur2->id,
                'product_id' => $products[3]->id,
                'quantity' => 20,
                'unit_price' => 95000,
                'subtotal' => 1900000,
            ]);

            PurchaseItem::create([
                'purchase_id' => $pur2->id,
                'product_id' => $products[5]->id,
                'quantity' => 5,
                'unit_price' => 420000,
                'subtotal' => 2100000,
            ]);
        }

        // 8. Sales (Penjualan / Transaksi Kasir)
        if (Sale::where('invoice_number', 'JL-202607-001')->doesntExist()) {
            $s1Total = (2 * 165000) + (1 * 105000); // 330,000 + 105,000 = 435,000
            $sale1 = Sale::create([
                'invoice_number' => 'JL-202607-001',
                'customer_id' => $customers[0]->id,
                'sale_date' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                'total_amount' => $s1Total,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'paid_amount' => $s1Total,
                'due_amount' => 0,
                'created_by' => $kasir->id,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $products[0]->id,
                'quantity' => 2,
                'unit_price' => 165000,
                'subtotal' => 330000,
            ]);

            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $products[2]->id,
                'quantity' => 1,
                'unit_price' => 105000,
                'subtotal' => 105000,
            ]);
        }

        if (Sale::where('invoice_number', 'JL-202607-002')->doesntExist()) {
            $s2Total = (1 * 520000) + (2 * 115000); // 520,000 + 230,000 = 750,000
            $sale2 = Sale::create([
                'invoice_number' => 'JL-202607-002',
                'customer_id' => $customers[2]->id,
                'sale_date' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'total_amount' => $s2Total,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'paid_amount' => $s2Total,
                'due_amount' => 0,
                'created_by' => $kasir->id,
            ]);

            SaleItem::create([
                'sale_id' => $sale2->id,
                'product_id' => $products[5]->id,
                'quantity' => 1,
                'unit_price' => 520000,
                'subtotal' => 520000,
            ]);

            SaleItem::create([
                'sale_id' => $sale2->id,
                'product_id' => $products[3]->id,
                'quantity' => 2,
                'unit_price' => 115000,
                'subtotal' => 230000,
            ]);
        }

        if (Sale::where('invoice_number', 'JL-202607-003')->doesntExist()) {
            $s3Total = (3 * 190000) + (2 * 85000); // 570,000 + 170,000 = 740,000
            $sale3 = Sale::create([
                'invoice_number' => 'JL-202607-003',
                'customer_id' => $customers[3]->id,
                'sale_date' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
                'total_amount' => $s3Total,
                'payment_method' => 'credit',
                'payment_status' => 'partial',
                'paid_amount' => 400000,
                'due_amount' => 340000,
                'created_by' => $kasir->id,
            ]);

            SaleItem::create([
                'sale_id' => $sale3->id,
                'product_id' => $products[1]->id,
                'quantity' => 3,
                'unit_price' => 190000,
                'subtotal' => 570000,
            ]);

            SaleItem::create([
                'sale_id' => $sale3->id,
                'product_id' => $products[6]->id,
                'quantity' => 2,
                'unit_price' => 85000,
                'subtotal' => 170000,
            ]);
        }

        // 9. Cash Transactions (Arus Kas)
        if (CashTransaction::count() === 0) {
            CashTransaction::create([
                'type' => 'in',
                'amount' => 1500000,
                'category' => 'Modal Kas Awal',
                'description' => 'Pemasukan modal awal kasir bulan Juli',
                'transaction_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'created_by' => $admin->id,
            ]);

            CashTransaction::create([
                'type' => 'out',
                'amount' => 175000,
                'category' => 'Biaya Operasional',
                'description' => 'Pembayaran tagihan listrik & air toko',
                'transaction_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'created_by' => $admin->id,
            ]);

            CashTransaction::create([
                'type' => 'out',
                'amount' => 60000,
                'category' => 'Konsumsi',
                'description' => 'Beli galon air minum & kopi kasir',
                'transaction_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'created_by' => $kasir->id,
            ]);
        }
    }
}
