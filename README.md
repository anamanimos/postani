# POS Toko Tani - Aplikasi Kasir Pertanian

Aplikasi POS (Point of Sale) mobile-first khusus untuk toko pertanian (jual pupuk, benih, pestisida, alat tani) berbasis Laravel 11, MySQL, Tailwind CSS (Glassmorphism theme), dan Alpine.js.

## Fitur Utama

1. **Kasir (Penjualan / POS)**
   - Desain responsif mobile-first, sangat cocok untuk handphone (≤420px).
   - Pencarian produk cepat dengan Alpine.js.
   - Stepper jumlah barang mudah digunakan di layar sentuh.
   - Pilihan metode pembayaran: **Tunai**, **QRIS**, **Transfer**, dan **Hutang (Kredit)**.

2. **Pembelian Tengkulak**
   - Pencatatan transaksi pembelian barang dari tengkulak.
   - Harga beli fluktuatif disimpan per transaksi di histori harga.
   - Menampilkan histori harga saat memasukkan barang baru sebagai referensi.
   - Pencatatan hutang toko jika bayar tempo/kredit ke tengkulak.

3. **Aturan Margin Otomatis**
   - Aturan margin minimum (default Rp 1.000) yang dikonfigurasi melalui menu Pengaturan.
   - Harga jual disesuaikan otomatis jika margin turun di bawah margin minimum akibat harga beli tengkulak yang naik.

4. **Konversi Satuan (Beli vs Jual)**
   - Mendukung satuan beli yang berbeda dengan satuan jual (misal: beli per kg, jual per karung).
   - Pengurangan stok otomatis disesuaikan secara akurat di latar belakang menggunakan `conversion_factor`.

5. **Buku Kas Manual**
   - Pencatatan uang masuk dan keluar manual untuk kebutuhan operasional harian toko.

6. **Laporan & Cetak Struk**
   - Rangkuman Penjualan harian/bulanan, Pembelian per tengkulak, Laba kotor (Omset dikurangi HPP), dan Hutang-Piutang.
   - Cetak struk belanja thermal (lebar 80mm) berformat PDF.

7. **Backup Otomatis ke Telegram**
   - Command scheduler harian untuk melakukan mysqldump database, kompresi gzip, dan mengirimkannya ke Telegram via Bot API.

---

## Panduan Instalasi (Lokal / Laragon)

1. **Clone repository ini ke folder Laragon `www`**:
   ```bash
   cd d:\laragon\www
   # pastikan folder project bernama `pos-tani`
   ```

2. **Setup Database**:
   - Nyalakan MySQL di Laragon.
   - Buat database baru bernama `pos_toko_tani`.

3. **Install Dependencies**:
   ```bash
   composer install
   npm install
   npm run build
   ```

4. **Migrasi & Seed Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Akses Aplikasi**:
   - Jika menggunakan Laragon, buka browser ke: `http://pos-tani.test`
   - Atau jalankan: `php artisan serve` dan buka `http://127.0.0.1:8000`

---

## Kredensial Default

- **Email**: `admin@postani.com`
- **Password**: `password`

---

## Konfigurasi Tambahan

### 1. Margin Minimum
Untuk mengubah batas margin minimum, masuk ke menu **Lainnya -> Pengaturan**, lalu ubah nilai **Margin Minimum Produk** (default 1000 rupiah).

### 2. Backup Telegram
Tambahkan variabel berikut ke file `.env` untuk mengaktifkan backup otomatis harian:
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```
Untuk menguji backup secara manual, jalankan command:
```bash
php artisan backup:telegram
```

---

## Pengujian Internal
Untuk memverifikasi logika alur transaksi secara otomatis (pembelian, perhitungan stok, kenaikan harga jual otomatis, pencegahan oversold), jalankan command berikut:
```bash
php artisan verify:logic
```
Semua logika bisnis penting akan disimulasikan dan divalidasi secara langsung ke database.
