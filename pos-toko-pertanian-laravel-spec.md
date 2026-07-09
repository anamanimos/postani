# SPESIFIKASI APLIKASI — POS TOKO PERTANIAN (Laravel + MySQL)

> Dokumen ini adalah **brief teknis siap eksekusi** untuk AI coding agent (mis. Antigravity / Claude Code / Cline).
> Tujuan: agar agent bisa langsung generate project Laravel tanpa banyak bertanya balik.
> Bahasa kode: Inggris (nama tabel/kolom/kelas). Bahasa UI: Indonesia.

---

## 1. Ringkasan Aplikasi

Aplikasi **POS (Point of Sale) mobile-first** untuk toko pertanian (jual pupuk, benih, pestisida, alat tani, dll), dengan dua alur utama:

1. **Penjualan (Sales)** — kasir menjual barang ke pelanggan/petani. Pembayaran: **Tunai, QRIS/Transfer, atau Hutang (piutang pelanggan)**.
2. **Pembelian dari Tengkulak (Purchasing)** — toko membeli stok dari tengkulak/supplier, di mana **harga beli bisa berbeda-beda setiap transaksi bahkan untuk tengkulak & barang yang sama** (fluktuatif, tergantung nego/musim). Sistem mencatat **histori harga per tengkulak** dan **hutang toko ke tengkulak** (jika pembayaran ke tengkulak juga bisa kredit/cicil).

Skala: **1 toko, 1 kasir (single user)** — jadi autentikasi cukup simpel (1 akun admin merangkap kasir), tapi struktur data tetap dibuat rapi agar mudah dikembangkan ke multi-user di masa depan.

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11.x (PHP 8.2+) |
| Database | MySQL 8.x |
| Frontend | Blade + Alpine.js + Tailwind CSS (mobile-first, tanpa build tool berat / pakai Vite bawaan Laravel) |
| Auth | Laravel Breeze (simple, session-based) |
| PDF Struk | `barryvdh/laravel-dompdf` |
| Backup | Laravel Scheduler + Telegram Bot API (kirim file `.sql` dump ke channel/chat Telegram, terjadwal harian) |
| Queue/Cache | Sync/File (tidak perlu Redis, aplikasi ringan) |
| Server target | **VPS Ubuntu 22.04 + Nginx + PHP-FPM 8.2 + MySQL 8**, dikelola via systemd/Supervisor untuk queue worker & scheduler (cron) |
| Tema UI | **Clean Glassmorphism** — dasar putih, aksen hijau (primary) & oranye (secondary/CTA), efek kaca (blur + transparansi tipis) pada card & bottom nav |

---

## 3. Struktur Database (MySQL)

### 3.1 Master Data

**`users`** (bawaan Laravel + tambahan)
```
id, name, email, password, role (enum: admin, kasir) default admin, phone, is_active, timestamps
```

**`categories`** — kategori produk (pupuk, benih, pestisida, alat, dll)
```
id, name, description, timestamps
```

**`units`** — satuan (kg, karung, liter, botol, ikat, pcs)
```
id, name (varchar), symbol (varchar, e.g. "kg"), timestamps
```

**`products`**
```
id, category_id (FK), sku (varchar, unique, nullable),
name,
buy_unit_id (FK units)          -- satuan saat DIBELI dari tengkulak, mis. "kg"
sell_unit_id (FK units)         -- satuan saat DIJUAL ke pelanggan, mis. "karung"
conversion_factor (decimal 12,4) default 1
                                 -- berapa buy_unit = 1 sell_unit. Contoh: 1 karung = 25 kg -> conversion_factor = 25
                                 -- jika buy_unit == sell_unit (tidak ada konversi), factor = 1
last_purchase_price (decimal 15,2) default 0   -- harga beli TERAKHIR per buy_unit (untuk HPP & tampilan cepat)
avg_purchase_price (decimal 15,2) default 0    -- rata-rata tertimbang harga beli per buy_unit (ditampilkan sebagai referensi, TIDAK dipakai untuk HPP)
selling_price (decimal 15,2)    -- harga jual per sell_unit, auto-adjust mengikuti aturan margin minimum (lihat §5.4)
stock (decimal 12,2)            -- stok disimpan dalam BUY_UNIT (satuan terkecil, mis. kg), agar akurat
min_stock (decimal 12,2) default 0  -- untuk alert stok menipis (dalam buy_unit)
image (varchar nullable)
is_active (boolean) default true
timestamps
```
> Stok selalu disimpan & dihitung dalam **buy_unit** (satuan terkecil/pembelian). Saat penjualan per **sell_unit**, sistem otomatis mengalikan dengan `conversion_factor` untuk memotong stok. Contoh: jual 1 karung (sell_unit) → stok berkurang 25 kg (buy_unit), jika `conversion_factor` = 25.

**`settings`** — pengaturan aplikasi yang bisa diubah admin
```
id, key (varchar unique, e.g. "min_margin"), value (varchar), description, timestamps
```
> Seed awal: `min_margin` = `1000` (rupiah). Nilai ini bisa diubah admin lewat halaman Pengaturan.

**`suppliers`** (tengkulak)
```
id, name, phone, address, notes, timestamps
```

**`customers`** (pelanggan/petani, untuk yang hutang/piutang)
```
id, name, phone, address, timestamps
```

### 3.2 Transaksi Pembelian ke Tengkulak

**`purchases`** (header transaksi beli dari tengkulak)
```
id, invoice_number (varchar, unique, auto e.g. PB-20260709-0001)
supplier_id (FK)
purchase_date (date)
total_amount (decimal 15,2)
payment_status (enum: paid, partial, unpaid) default unpaid
paid_amount (decimal 15,2) default 0
due_amount (decimal 15,2) default 0   -- hutang toko ke tengkulak
notes (text nullable)
created_by (FK users)
timestamps
```

**`purchase_items`** (detail barang per transaksi pembelian — INI YANG MENAMPUNG HARGA BEDA-BEDA)
```
id, purchase_id (FK)
product_id (FK)
quantity (decimal 12,2)     -- dalam buy_unit produk (mis. kg)
unit_price (decimal 15,2)   -- harga beli per buy_unit KHUSUS transaksi ini, bisa beda tiap kali walau tengkulak & produk sama
subtotal (decimal 15,2)     -- quantity * unit_price
timestamps
```

**`purchase_price_history`** (histori harga per tengkulak per produk — untuk analisa & referensi nego berikutnya)
```
id, supplier_id (FK), product_id (FK), unit_price (decimal 15,2), purchase_date (date), purchase_item_id (FK)
timestamps
```
> Diisi otomatis setiap kali `purchase_items` baru dibuat (event/observer), sehingga kasir bisa lihat "riwayat harga tengkulak X untuk barang Y" saat input pembelian baru.

**`supplier_payments`** (cicilan/pelunasan hutang toko ke tengkulak)
```
id, purchase_id (FK), amount (decimal 15,2), payment_date (date), payment_method (enum: cash, transfer), notes, created_by (FK users)
timestamps
```

### 3.3 Transaksi Penjualan (Kasir)

**`sales`** (header transaksi jual)
```
id, invoice_number (varchar unique, e.g. PJ-20260709-0001)
customer_id (FK nullable — bisa jual tanpa nama pelanggan/walk-in)
sale_date (datetime)
total_amount (decimal 15,2)
payment_method (enum: cash, qris, transfer, credit)  -- 'credit' = hutang pelanggan
payment_status (enum: paid, partial, unpaid) default paid
paid_amount (decimal 15,2) default 0
due_amount (decimal 15,2) default 0
created_by (FK users)
timestamps
```

**`sale_items`**
```
id, sale_id (FK), product_id (FK), quantity (decimal 12,2), unit_price (decimal 15,2), subtotal (decimal 15,2)
timestamps
```

**`customer_payments`** (cicilan piutang pelanggan)
```
id, sale_id (FK), amount (decimal 15,2), payment_date (date), payment_method (enum: cash, qris, transfer), notes, created_by (FK users)
timestamps
```

### 3.4 Kas / Cashflow (opsional tapi disarankan)

**`cash_transactions`** — mutasi kas manual (biaya operasional, ambil untung, dll), terpisah dari sales/purchases otomatis
```
id, type (enum: in, out), amount (decimal 15,2), category (varchar, e.g. "operasional", "listrik", "gaji")
description, transaction_date (date), created_by (FK users)
timestamps
```

---

## 4. Relasi Model (Eloquent)

```
Category      hasMany   Product
Unit          hasMany   Product (via buy_unit_id AND sell_unit_id -- 2 relasi terpisah: buyUnit(), sellUnit())
Product       belongsTo Category, Unit (buyUnit, sellUnit)
              hasMany   PurchaseItem, SaleItem, PurchasePriceHistory

Supplier      hasMany   Purchase, SupplierPayment (via Purchase)
Purchase      belongsTo Supplier, User(created_by)
              hasMany   PurchaseItem, SupplierPayment

Customer      hasMany   Sale, CustomerPayment (via Sale)
Sale          belongsTo Customer(nullable), User(created_by)
              hasMany   SaleItem, CustomerPayment

Setting       -- key/value tunggal, diakses via helper Setting::get('min_margin', 1000)
```

---

## 5. Alur Bisnis Kunci

### 5.1 Pembelian dari Tengkulak
1. Kasir/admin pilih tengkulak (atau buat baru).
2. Tambah item: pilih produk → **sistem tampilkan histori harga dari tengkulak ini untuk produk tsb**, berupa dua angka:
   - **Harga terakhir** (last_purchase_price) — angka utama yang ditonjolkan, jadi acuan HPP.
   - **Rata-rata** (avg_purchase_price) — ditampilkan lebih kecil di bawahnya sebagai referensi tren, tidak dipakai untuk hitung apapun secara otomatis.
   Kasir input qty (dalam buy_unit, mis. kg) & harga beli aktual — bebas beda dari histori.
3. Simpan → otomatis:
   - `stock` bertambah (dalam buy_unit).
   - `last_purchase_price` = harga di transaksi ini.
   - `avg_purchase_price` dihitung ulang sebagai rata-rata tertimbang dari seluruh histori (`SUM(qty*price)/SUM(qty)`).
   - Baris baru masuk ke `purchase_price_history`.
   - **Cek margin (lihat §5.4)** → `selling_price` disesuaikan otomatis jika perlu.
   - Jika bayar sebagian/tidak bayar → `due_amount` tercatat sebagai hutang ke tengkulak, muncul di halaman "Hutang ke Tengkulak" untuk dicicil via `supplier_payments`.

### 5.2 Penjualan
1. Kasir cari produk berdasarkan **nama** (tanpa barcode/scan) → tambah ke keranjang.
2. Qty diinput dalam **sell_unit** produk (mis. "2 karung"); sistem otomatis konversi ke buy_unit untuk validasi & pemotongan stok (`qty_sell_unit × conversion_factor`).
3. Pilih metode bayar: Tunai / QRIS / Transfer / **Hutang** (wajib pilih pelanggan jika hutang).
4. Simpan → stok berkurang otomatis (dalam buy_unit), generate PDF struk (`barryvdh/laravel-dompdf`) yang bisa diunduh/dibagikan.
5. Jika hutang → masuk ke halaman "Piutang Pelanggan", dicicil via `customer_payments`.

### 5.4 Aturan Margin Minimum Otomatis
- Setiap kali `last_purchase_price` produk berubah (ada pembelian baru), sistem hitung: `margin = selling_price - (last_purchase_price × conversion_factor)`.
- Jika `margin < min_margin` (ambil dari tabel `settings`, default **Rp 1.000**, bisa diubah admin) → sistem **otomatis set** `selling_price = (last_purchase_price × conversion_factor) + min_margin`.
- Jika margin sudah ≥ `min_margin`, harga jual **tidak diubah** (supaya harga jual yang sudah dinaikkan admin secara manual tidak turun tiba-tiba).
- Setiap penyesuaian otomatis ini dicatat di log singkat (kolom `notes` pada produk, atau tabel log terpisah jika ingin lebih rapi) supaya admin tahu harga jual berubah karena aturan margin, bukan human error.

### 5.5 Validasi Stok
- Tidak boleh jual melebihi stok tersedia dalam buy_unit (setelah dikonversi dari sell_unit), kecuali admin override dengan alasan.
- Alert dashboard jika `stock <= min_stock`.

### 5.6 Backup Otomatis ke Telegram
- Laravel Scheduler menjalankan command `backup:telegram` setiap hari (jam malam, mis. 23:00) via `mysqldump`.
- File `.sql` (di-gzip) dikirim ke chat/channel Telegram menggunakan Telegram Bot API (`sendDocument`), memakai `TELEGRAM_BOT_TOKEN` & `TELEGRAM_CHAT_ID` di `.env`.
- Simpan juga 3 backup terakhir secara lokal di server (`storage/app/backups`) sebagai lapisan kedua, lalu hapus yang lebih lama dari 7 hari agar tidak memenuhi disk VPS.

---

## 6. Fitur Aplikasi (Checklist)

- [ ] Login (single user, role admin merangkap kasir)
- [ ] Dashboard: ringkasan penjualan hari ini, hutang piutang, stok menipis
- [ ] Master Produk (CRUD + kategori + satuan + upload foto)
- [ ] Master Tengkulak (CRUD)
- [ ] Master Pelanggan (CRUD)
- [ ] Transaksi Pembelian (dengan histori harga per tengkulak)
- [ ] Cicilan/Pelunasan Hutang ke Tengkulak
- [ ] Transaksi Penjualan / kasir (UI mobile-first, cepat, big buttons)
- [ ] Cicilan/Pelunasan Piutang Pelanggan
- [ ] Cetak/Preview Struk (PDF atau share WhatsApp)
- [ ] Laporan: Penjualan harian/bulanan, Pembelian per tengkulak, Laba kotor, Hutang-Piutang outstanding
- [ ] Kas manual (in/out) — opsional fase 2
- [ ] Export laporan ke Excel/PDF — opsional fase 2

---

## 7. UI/UX — Mobile First

Prinsip: layar HP dulu (360–420px), bottom navigation, tombol besar, minim ketik (pakai stepper qty +/-).

**Bottom nav (4 menu utama):**
`🏠 Dashboard` · `🛒 Jual` · `📦 Beli` · `📊 Laporan`

**Halaman Jual (kasir):**
- Search bar produk di atas (autofocus)
- Grid/list produk dengan foto + harga + stok
- Tap produk → masuk keranjang (floating cart button dengan badge jumlah item & total)
- Halaman keranjang: qty stepper, tombol metode bayar besar (Tunai/QRIS/Transfer/Hutang), tombol "Selesai" full-width

**Halaman Beli (dari tengkulak):**
- Pilih/tambah tengkulak
- Tambah produk → tampil chip histori harga terakhir (tap untuk auto-isi)
- Input qty & harga → subtotal otomatis
- Ringkasan total + status bayar (lunas/cicil/hutang)

**Warna & style — Clean Glassmorphism (Putih · Hijau · Oranye):**
- Background dasar: putih/off-white (`#FAFAF9`) dengan gradasi lembut hijau muda di beberapa area (bukan flat putih polos).
- Card/panel: efek kaca — `background: rgba(255,255,255,0.6)`, `backdrop-filter: blur(12px)`, border tipis `rgba(255,255,255,0.4)`, shadow lembut, radius besar (16–20px).
- Warna primer (aksi utama, header, ikon aktif): **hijau** (mis. `#16A34A` / emerald-600).
- Warna aksen (tombol CTA penting, badge hutang/alert, tombol "Bayar"): **oranye** (mis. `#F97316` / orange-500).
- Teks tetap gelap kontras tinggi (`#1F2937`) di atas kaca transparan agar tetap terbaca di luar ruangan/sinar matahari — glassmorphism jangan mengorbankan keterbacaan.
- Bottom navigation: bar kaca mengambang (floating, rounded, blur) dengan ikon aktif berwarna hijau dan indikator kecil oranye.
- Ikuti juga panduan `frontend-design` untuk konsistensi tipografi & spacing, sesuaikan token warna ke palet di atas.

---

## 8. Struktur Folder Laravel (ringkas)

```
app/
  Models/ (Product, Category, Unit, Supplier, Customer, Purchase, PurchaseItem,
            PurchasePriceHistory, SupplierPayment, Sale, SaleItem, CustomerPayment,
            CashTransaction, Setting)
  Http/Controllers/
    DashboardController.php
    ProductController.php
    SupplierController.php
    CustomerController.php
    PurchaseController.php
    SaleController.php
    PaymentController.php (handle supplier & customer payment)
    ReportController.php
    SettingController.php (ubah min_margin & pengaturan lain)
  Observers/
    PurchaseItemObserver.php  (auto update stock, last/avg purchase price, price history, cek margin §5.4)
    SaleItemObserver.php      (konversi sell_unit→buy_unit, decrement stock, validasi stok)
  Console/Commands/
    BackupToTelegram.php      (dump MySQL, gzip, kirim via Telegram Bot API, jadwalkan di Kernel/routes/console.php)
database/
  migrations/ (sesuai tabel di atas, termasuk kolom buy_unit_id/sell_unit_id/conversion_factor & tabel settings)
  seeders/ (UnitSeeder, CategorySeeder, SettingSeeder (min_margin=1000), AdminUserSeeder)
resources/views/
  layouts/app.blade.php (mobile-first glassmorphism shell + floating bottom nav)
  dashboard/, products/, suppliers/, customers/, purchases/, sales/, reports/, settings/
routes/web.php
routes/console.php  (schedule: BackupToTelegram daily 23:00)
```

---

## 9. Keputusan Final (Semua Pertanyaan Sudah Terjawab)

| Pertanyaan | Jawaban |
|---|---|
| Skala pemakaian | 1 toko, 1 kasir (single user) |
| Metode pembayaran | Tunai + QRIS/Transfer + Hutang |
| Detail pembelian tengkulak | Lengkap — histori harga per tengkulak + hutang ke tengkulak |
| Harga jual otomatis | Ya, dengan aturan margin minimum (default Rp 1.000, bisa diubah admin di halaman Pengaturan) — lihat §5.4 |
| Metode hitung HPP | Harga beli **terakhir**; rata-rata tertimbang tetap dihitung & ditampilkan sebagai referensi (tidak dipakai untuk HPP) |
| Barcode/scan | Tidak — pencarian produk berdasarkan **nama saja** |
| Cetak struk | **PDF** (`barryvdh/laravel-dompdf`), tanpa printer thermal |
| Limit/approval hutang | Tidak ada batas maksimal piutang |
| Backup data | **Ya** — backup otomatis harian ke **Telegram** via Bot API + salinan lokal 7 hari |
| Konversi satuan beli↔jual | **Ya, sangat perlu** — via `conversion_factor` pada tabel `products` (lihat §3.1 & §5.2) |
| Hosting target | **VPS** Ubuntu + Nginx + PHP-FPM + MySQL, akses SSH penuh |
| Tema UI | **Clean Glassmorphism**, palet putih · hijau · oranye (lihat §7) |

Tidak ada lagi pertanyaan terbuka — dokumen ini sudah siap dipakai langsung sebagai brief eksekusi.

---

## 10. Urutan Eksekusi untuk AI Agent

1. `laravel new pos-toko-tani` + install Breeze (Blade + Alpine) + Tailwind, konfigurasi token warna glassmorphism putih/hijau/oranye (§7).
2. Setup `.env` MySQL, buat database `pos_toko_tani`. Tambah variabel `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`.
3. Buat semua migration sesuai skema §3 (termasuk `buy_unit_id`, `sell_unit_id`, `conversion_factor`, tabel `settings`), jalankan `migrate`.
4. Buat model + relasi sesuai §4, termasuk helper `Setting::get('min_margin', 1000)`.
5. Buat seeder: unit dasar (kg, karung, liter, pcs, ikat), kategori dasar (Pupuk, Benih, Pestisida, Alat), setting `min_margin=1000`, 1 user admin.
6. Buat Observer untuk auto-update stok, konversi satuan, histori harga, dan aturan margin otomatis (§5.1–§5.4).
7. Buat controller & routes untuk tiap modul termasuk `SettingController` (§8).
8. Buat layout mobile-first glassmorphism + floating bottom nav (§7), lalu view per modul.
9. Implementasi modul Pembelian → Penjualan → Pembayaran/Cicilan → Laporan (urutan prioritas ini karena saling bergantung).
10. Buat command `BackupToTelegram` + jadwalkan harian di `routes/console.php` (§5.6).
11. Uji alur end-to-end: beli stok dari tengkulak (2x dengan harga beda) → cek `last_purchase_price`/`avg_purchase_price` & auto-adjust `selling_price` sesuai margin → jual ke pelanggan pakai sell_unit → cek stok terkonversi & berkurang benar → laporan laba kotor benar.
12. Setup deployment di **VPS**: Nginx + PHP-FPM 8.2 + MySQL 8, systemd service untuk queue worker, cron untuk Laravel Scheduler, SSL via Certbot.
13. Tulis README berisi cara install, kredensial default, cara set `TELEGRAM_BOT_TOKEN`, dan cara ubah `min_margin` di halaman Pengaturan.

---

## 11. Kriteria Selesai (Acceptance Criteria)

- Bisa input pembelian dari tengkulak yang sama, produk yang sama, dengan **harga beli berbeda di dua transaksi berbeda**, dan keduanya tercatat terpisah di `purchase_price_history`; `last_purchase_price` & `avg_purchase_price` ter-update benar.
- Produk dengan `conversion_factor` (mis. 1 karung = 25 kg) — pembelian dalam kg, penjualan dalam karung, stok tetap konsisten dalam satuan kg.
- Saat margin (`selling_price - last_purchase_price×conversion_factor`) di bawah `min_margin` (default Rp 1.000), `selling_price` otomatis naik; jika sudah di atas, tidak berubah.
- Stok produk bertambah setelah pembelian, berkurang setelah penjualan (dengan konversi satuan benar), tidak bisa minus.
- Transaksi jual dengan metode "Hutang" muncul di daftar piutang pelanggan dan bisa dicicil sampai lunas.
- Transaksi beli dengan status "belum lunas" muncul di daftar hutang ke tengkulak dan bisa dicicil.
- Backup harian terkirim otomatis ke Telegram, bisa diverifikasi manual dengan trigger command lebih awal.
- Semua halaman transaksi nyaman dipakai di layar HP (≤420px), tema glassmorphism putih/hijau/oranye konsisten, maksimal 2 tap untuk input barang di keranjang.
- Laporan penjualan & pembelian bisa difilter per tanggal/per tengkulak/per pelanggan.
- Aplikasi berjalan normal di VPS Ubuntu (Nginx + PHP-FPM + MySQL) dengan HTTPS aktif.
