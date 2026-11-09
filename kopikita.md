# 📘 DOKUMENTASI TEKNIS & PANDUAN PENGEMBANGAN: KOPI KITA POS

> **Sistem Point of Sale (POS) Kafe & Restoran Berbasis Web**  
> Standar Uji Kompetensi Keahlian (UKK) — Skema Junior Web Programmer  
> Framework: **Laravel 11** | Styling: **Tailwind CSS** | Database: **MySQL / SQLite**

---

## DAFTAR ISI
1. [Ringkasan Eksekutif & Gambaran Aplikasi](#1-ringkasan-eksekutif--gambaran-aplikasi)
2. [Spesifikasi Teknologi & Arsitektur](#2-spesifikasi-teknologi--arsitektur)
3. [Struktur Direktori & Penjelasan File Proyek](#3-struktur-direktori--penjelasan-file-proyek)
4. [Struktur Basis Data & Model Eloquent](#4-struktur-basis-data--model-eloquent)
5. [Konfigurasi Terpusat (Un-hardcoded Architecture)](#5-konfigurasi-terpusat-un-hardcoded-architecture)
6. [Modul Utama & Fitur Unggulan](#6-modul-utama--fitur-unggulan)
7. [Alur Pembuatan Sistem dari Awal hingga Akhir](#7-alur-pembuatan-sistem-dari-awal-hingga-akhir)
8. [Automated Testing & Jaminan Kualitas](#8-automated-testing--jaminan-kualitas)
9. [Panduan Instalasi & Menjalankan Proyek](#9-panduan-instalasi--menjalankan-proyek)
10. [Bank Tanya Jawab Teknis (Assessor Q&A)](#10-bank-tanya-jawab-teknis-assessor-qa)

---

## 1. RINGKASAN EKSEKUTIF & GAMBARAN APLIKASI

**Kopi Kita POS** adalah aplikasi kasir modern yang dirancang untuk operasional harian kedai kopi dan restoran. Sistem ini mengadopsi antarmuka pengguna responsif bernuansa *Navy Slate* dan aksen *Orange Warm*, berpedoman pada mockup POS modern dengan prinsip **Single-Role Cashier Architecture**.

### Nilai Utama Sistem:
- **Single-Role Focus (`kasir`)**: Seluruh alur kerja difokuskan untuk kasir tanpa menu admin atau tombol demo yang mengganggu, menciptakan pengalaman operasional yang realistis dan aman.
- **Zero Hardcoded Values**: Seluruh metadata kafe, persentase pajak PB1 (10%), ambang batas stok kritis, dan versi aplikasi dikontrol melalui file konfigurasi `config/pos.php`.
- **Checkout Asinkron Cepat (AJAX)**: Transaksi diproses tanpa me-reload halaman, otomatis memotong stok langsung di tampilan (DOM), dan menampilkan modal struk instan pasca-pembayaran.
- **Analitik Arus Kas Interaktif**: Dashboard dilengkapi grafik *Chart.js* multi-rentang (Hari Ini per jam, Minggu Ini per hari, Bulan Ini per minggu) secara dinamis.
- **Kepatuhan Pajak & Audit Finansial**: Menerapkan Pajak Restoran PB1 10% dan *Data Snapshot* pada riwayat transaksi untuk integritas pembukuan jangka panjang.
- **100% Automated Test Coverage**: Dilengkapi dengan unit & feature testing terotomatisasi mencakup autentikasi, dashboard, transaksi kasir, manajemen menu, hingga laporan.

---

## 2. SPESIFIKASI TEKNOLOGI & ARSITEKTUR

| Komponen | Teknologi | Penjelasan Teknis |
|---|---|---|
| **Backend Framework** | Laravel 11 (PHP 8.3+) | Menerapkan arsitektur MVC, Eloquent ORM, Database Transactions, dan Form Request Validation. |
| **Frontend Engine** | Laravel Blade Templates | Server-side rendering cepat dengan component inheritance dan layout modular. |
| **CSS Framework** | Tailwind CSS 3.x | Desain antarmuka modern, custom utility classes, dan palet warna presisi sesuai mockup. |
| **Database** | MySQL / MariaDB (Produksi) & SQLite (Testing) | Basis data relasional dengan indeks performa, foreign keys, dan cascade rules. |
| **Interaktivitas UI** | Vanilla JavaScript (ES6+) & Fetch API | Penanganan keranjang belanja POS, kalkulasi pajak/kembalian real-time, Hold Order, dan AJAX checkout. |
| **Visualisasi Data** | Chart.js 4.x via CDN | Rendering grafik performa penjualan harian, mingguan, dan bulanan. |
| **Ekspor Dokumen** | `barryvdh/laravel-dompdf` | Pembuatan file PDF untuk struk thermal (58mm/80mm) dan rekap laporan penjualan. |
| **Format Pertukaran** | CSV (Comma-Separated Values) | Import data menu secara native dan export rekap penjualan untuk spreadsheet. |
| **Testing Harness** | PHPUnit 11 | Pengujian fungsional otomatis dengan SQLite in-memory database (`:memory:`). |

---

## 3. STRUKTUR DIREKTORI & PENJELASAN FILE PROYEK

Berikut adalah peta struktur direktori utama beserta peran fungsional setiap file:

```
kopikita/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Otentikasi login, session, dan logout kasir
│   │   │   ├── DashboardController.php     # Agregasi metrik omzet, grafik per jam/minggu/bulan
│   │   │   ├── LaporanController.php       # Filter laporan penjualan, cetak struk thermal & export
│   │   │   ├── MenuController.php          # CRUD menu, upload foto, export & import CSV
│   │   │   └── TransaksiController.php     # Terminal POS, kalkulasi keranjang, validasi stok & AJAX
│   │   └── Middleware/                     # Proteksi rute session autentikasi
│   └── Models/
│       ├── Menu.php                        # Entitas produk, accessor status stok, mutator harga
│       ├── Transaksi.php                   # Entitas transaksi, relasi kasir dan detail item
│       ├── TransaksiItem.php               # Baris pesanan (snapshot harga & nama produk)
│       └── User.php                        # Entitas kasir (autentikasi dan identitas)
├── config/
│   ├── app.php                             # Konfigurasi aplikasi & timezone (Asia/Jakarta)
│   └── pos.php                             # Konfigurasi terpusat: nama kafe, PB1, batas stok, versi
├── database/
│   ├── factories/
│   │   └── UserFactory.php                 # Factory pembuatan akun kasir untuk kebutuhan testing
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_11_09_023813_add_username_role_to_users_table.php
│   │   ├── 2026_11_09_023814_create_menus_table.php
│   │   ├── 2026_11_09_023815_create_transaksis_table.php
│   │   └── 2026_11_09_023816_create_transaksi_items_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php              # Master seeder orkestrasi
│       ├── UserSeeder.php                  # Seeder akun kasir utama
│       ├── MenuSeeder.php                  # Seeder katalog kopi, makanan, snack
│       └── TransaksiSeeder.php             # Seeder data transaksi historis untuk analitik
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php               # Master layout utama (sidebar, topbar, user badge)
│       │   └── auth.blade.php              # Layout khusus halaman login kasir
│       ├── auth/
│       │   └── login.blade.php             # Form login elegan dengan toggle visibility password
│       ├── dashboard/
│       │   └── index.blade.php             # Dashboard metrik penjualan & Chart.js cash flow switcher
│       ├── pos/
│       │   └── index.blade.php             # Terminal kasir, keranjang live, hold order, modal bayar
│       ├── menu/
│       │   └── index.blade.php             # Manajemen menu, modal form CRUD, native file picker CSV
│       └── laporan/
│           ├── index.blade.php             # Split-view riwayat transaksi & live thermal receipt preview
│           ├── struk_pdf.blade.php         # Template thermal receipt 58mm/80mm untuk DomPDF
│           └── rekap_pdf.blade.php         # Template rekapitulasi audit laporan penjualan PDF
├── routes/
│   └── web.php                             # Definisi rute aplikasi dengan proteksi middleware auth
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php                    # Pengujian login, validasi password, session, logout
│   │   ├── DashboardTest.php               # Pengujian metrik dashboard, label jam/minggu/bulan
│   │   ├── TransaksiPosTest.php            # Pengujian POS AJAX checkout, pemotongan stok, validasi bayar
│   │   ├── MenuTest.php                    # Pengujian CRUD menu, export CSV, native import CSV
│   │   ├── LaporanTest.php                 # Pengujian sorting urutan terbaru, PDF struk, export CSV
│   │   └── ExampleTest.php                 # Pengujian redirect tamu tanpa autentikasi
│   └── TestCase.php                        # Base test configuration
├── phpunit.xml                             # Konfigurasi pengujian PHPUnit (SQLite in-memory)
└── kopikita.md                             # Dokumentasi komprehensif sistem (file ini)
```

---

## 4. STRUKTUR BASIS DATA & MODEL ELOQUENT

Sistem menggunakan 4 tabel inti yang saling berelasi dengan aturan integritas referensial.

```
┌──────────────┐       1:N       ┌────────────────┐       1:N       ┌──────────────────────┐
│    users     │────────────────<│   transaksis   │────────────────<│   transaksi_items    │
│ (Kasir Auth) │                 │ (Header Trx)   │                 │ (Snapshot Baris Item)│
└──────────────┘                 └────────────────┘                 └──────────────────────┘
                                                                               │
                                                                         M:1   │
                                                                    ┌──────────────────────┐
                                                                    │        menus         │
                                                                    │   (Katalog Produk)   │
                                                                    └──────────────────────┘
```

### 1. Tabel `users`
Menyimpan kredensial dan profil kasir.
```sql
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('kasir') NOT NULL DEFAULT 'kasir',
  `avatar` VARCHAR(255) NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);
```

### 2. Tabel `menus`
Menyimpan katalog produk kuliner, harga, dan ketersediaan stok fisik.
```sql
CREATE TABLE `menus` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `nama` VARCHAR(100) NOT NULL,
  `kategori` ENUM('coffee', 'non-coffee', 'makanan', 'snack') NOT NULL,
  `harga_jual` DECIMAL(12, 2) NOT NULL,
  `harga_modal` DECIMAL(12, 2) NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);
```

### 3. Tabel `transaksis`
Menyimpan transaksi header, identitas pelanggan, jenis pesanan, dan perhitungan keuangan.
```sql
CREATE TABLE `transaksis` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `nama_pelanggan` VARCHAR(100) NULL,
  `nomor_meja` VARCHAR(50) NULL,
  `tipe_pesanan` ENUM('dine-in', 'takeaway') NOT NULL DEFAULT 'dine-in',
  `subtotal` DECIMAL(12, 2) NOT NULL,
  `pajak` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `diskon` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `total_harga` DECIMAL(12, 2) NOT NULL,
  `metode_pembayaran` ENUM('tunai', 'qris', 'debit') NOT NULL DEFAULT 'tunai',
  `bayar` DECIMAL(12, 2) NOT NULL,
  `kembalian` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `status` ENUM('selesai', 'diproses', 'batal') NOT NULL DEFAULT 'selesai',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

### 4. Tabel `transaksi_items`
Menyimpan rincian item pesanan. Menggunakan strategi **Financial Snapshot** di mana nama produk dan harga jual disimpan langsung pada saat transaksi dibuat agar laporan historis tidak terdistorsi jika di kemudian hari data produk diubah.
```sql
CREATE TABLE `transaksi_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `transaksi_id` BIGINT UNSIGNED NOT NULL,
  `menu_id` BIGINT UNSIGNED NULL,
  `nama_menu` VARCHAR(100) NOT NULL,
  `harga` DECIMAL(12, 2) NOT NULL,
  `qty` INT NOT NULL DEFAULT 1,
  `catatan` VARCHAR(255) NULL,
  `subtotal` DECIMAL(12, 2) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`transaksi_id`) REFERENCES `transaksis` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL
);
```

---

## 5. KONFIGURASI TERPUSAT (UN-HARDCODED ARCHITECTURE)

Sistem mengadopsi prinsip *Clean Architecture* dengan memusatkan seluruh konfigurasi operasional di `config/pos.php`. Tidak ada teks atau angka pajak yang ditulis manual (*hardcoded*) di dalam file Controller maupun View Blade.

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas & Metadata Outlet Kafe
    |--------------------------------------------------------------------------
    */
    'store_name' => env('POS_STORE_NAME', 'Kopi Kita'),
    'tagline' => env('POS_STORE_TAGLINE', 'Premium Artisan Coffee & Eatery'),
    'address' => env('POS_STORE_ADDRESS', 'Jl. Pemuda No. 45, Pati, Jawa Tengah'),
    'phone' => env('POS_STORE_PHONE', '0812-3456-7890'),
    'city' => env('POS_STORE_CITY', 'Pati, Jawa Tengah'),
    'footer_note' => env('POS_FOOTER_NOTE', 'Terima kasih atas kunjungan Anda. Selamat menikmati!'),
    'tax_id' => env('POS_TAX_ID', 'NPWPD: 4.012.345.6.789'),

    /*
    |--------------------------------------------------------------------------
    | Parameter Finansial & Pajak Restoran
    |--------------------------------------------------------------------------
    */
    'tax_rate' => (float) env('POS_TAX_RATE', 0.10), // Pajak Restoran PB1 10%
    'currency_symbol' => 'Rp',

    /*
    |--------------------------------------------------------------------------
    | Ambang Batas Stok Menipis
    |--------------------------------------------------------------------------
    */
    'low_stock_threshold' => (int) env('POS_LOW_STOCK_THRESHOLD', 15),

    /*
    |--------------------------------------------------------------------------
    | Versi Sistem
    |--------------------------------------------------------------------------
    */
    'version' => 'v2.4.0',
    'app_label' => 'Kopi Kita POS',
];
```

Contoh pemanggilan di seluruh aplikasi:
- Pajak transaksi: `config('pos.tax_rate', 0.10)`
- Ambang batas stok kritis: `config('pos.low_stock_threshold', 15)`
- Nama outlet pada struk dan header: `config('pos.store_name')`
- Alamat dan nomor kontak: `config('pos.address')` & `config('pos.phone')`

---

## 6. MODUL UTAMA & FITUR UNGGULAN

### 1. Modul Otentikasi Kasir (`/login`)
- Mendukung autentikasi via **Username** (`kasir`) atau **Email** (`kasir@kopikita.com`).
- Enkripsi password menggunakan algoritma Bcrypt standar industri.
- Desain *split screen*: visual branding di kiri dan form login modern di kanan dengan fitur toggle mata (show/hide password).
- Validasi input dengan pesan error berbahasa Indonesia yang jelas.

### 2. Modul Dashboard Analitik (`/`)
- **4 Kartu Metrik Real-time**: Total Penjualan Hari Ini, Jumlah Transaksi, Menu Terdaftar (dengan indikator stok menipis), dan Total Pesanan Terlayani.
- **Dynamic Cash Flow Switcher (Chart.js)**:
  - Tab *Hari Ini*: Menampilkan kurva penjualan per jam (08:00 - 22:00) menggunakan rentang waktu database-agnostik (`whereBetween`).
  - Tab *Minggu Ini*: Menampilkan omzet harian Senin s.d. Minggu secara dinamis.
  - Tab *Bulan Ini*: Menampilkan perbandingan performa per kuadran minggu (1-7, 8-14, 15-21, 22-28, 29+).
- **Top 5 Menu Terlaris**: Visualisasi produk favorit pelanggan beserta kuantitas dan nominal penjualan.
- **5 Transaksi Terkini**: Ringkasan transaksi tercepat untuk pemantauan arus kas.

### 3. Modul Terminal Kasir POS (`/pos`)
- **Katalog Menu Cepat**: Filter kategori instan (Semua, Coffee, Non-Coffee, Makanan, Snack) dan pencarian live.
- **Indikator Stok Otomatis**: Item dengan stok $\le 15$ diberi badge peringatan oranye; item dengan stok 0 otomatis diberi status *Habis* dan tombol dinonaktifkan.
- **Keranjang Pesanan Interaktif**:
  - Pilihan tipe pesanan (*Dine In* vs *Take Away*) dan input nomor meja / nama tamu.
  - Penambahan catatan khusus tiap item (*Less Ice*, *Less Sugar*, dll.).
  - Kalkulasi subtotal, PB1 10%, dan total belanja secara langsung.
  - Deteksi validasi uang tunai: tombol bayar otomatis memvalidasi kecukupan uang kasir.
- **Fitur Hold Order**: Kasir dapat menahan transaksi saat pelanggan masih memilih menu tambahan. Jumlah order tertahan dimonitor via icon badge di topbar dan dapat dipanggil kembali (*resume*) kapan saja.
- **AJAX Seamless Checkout**:
  - Transaksi diproses di latar belakang tanpa reload halaman.
  - Stok pada kartu menu langsung berkurang secara visual di DOM.
  - Modal struk interaktif otomatis muncul menampilkan rincian pembayaran, tombol cetak thermal, dan tombol unduh PDF.

### 4. Modul Manajemen Menu CRUD (`/menu`)
- **Operasi Lengkap**: Tambah menu baru, ubah rincian/harga/stok, dan hapus menu dengan konfirmasi aman.
- **Upload Gambar**: Dukungan upload gambar produk yang tersimpan rapi di direktori storage publik dengan fallback gambar otomatis.
- **Export & Import CSV**:
  - Tombol **Export CSV** untuk backup katalog produk sewaktu-waktu.
  - Tombol **Import CSV** terintegrasi dengan *Native File Picker* peramban, memproses berkas CSV dengan logika upsert (memperbarui produk jika SKU sudah ada atau membuat produk baru jika belum terdaftar).

### 5. Modul Laporan Penjualan & Struk Thermal (`/laporan`)
- **Urutan Kronologis Terjamin**: Tabel riwayat penjualan diurutkan ketat dari transaksi paling baru ke paling lama (`ORDER BY created_at DESC, id DESC`).
- **Pratinjau Struk Live (Split-View)**: Mengklik baris transaksi di sebelah kiri langsung memperbarui tampilan struk thermal di sisi kanan secara instan.
- **Export Multi-Format**:
  - Cetak struk kasir thermal via `window.print()` dengan CSS khusus print (menghilangkan elemen UI dan membatasi lebar kertas ke standar printer thermal).
  - Unduh PDF struk kasir individual via DomPDF (`/laporan/{id}/struk`).
  - Unduh Rekap Laporan Penjualan PDF (`/laporan/export/pdf`).
  - Unduh Rekap Laporan Penjualan CSV (`/laporan/export/csv`).

---

## 7. ALUR PEMBUATAN SISTEM DARI AWAL HINGGA AKHIR

Berikut adalah reka jejak kronologis dan metodologi rekayasa perangkat lunak yang diterapkan dalam membangun Kopi Kita POS:

```
[Tahap 1: Inisialisasi] ──> [Tahap 2: Skema DB] ──> [Tahap 3: Model & Relasi]
                                                              │
[Tahap 6: Controller]   <── [Tahap 5: Konfigurasi] <── [Tahap 4: Seeder Data]
         │
         ▼
[Tahap 7: UI & Blade]   ──> [Tahap 8: AJAX POS] ───> [Tahap 9: DomPDF & CSV]
                                                              │
[Tahap 12: Deployment]  <── [Tahap 11: Testing] <── [Tahap 10: Refactoring]
```

### Tahap 1: Inisialisasi Proyek & Lingkungan Pengembangan
1. Menginstal framework Laravel 11 dengan PHP 8.3+.
2. Mengonfigurasi file `.env` untuk koneksi basis data MySQL lokal pada web server XAMPP (`DB_DATABASE=kopi_kita`).
3. Mengonfigurasi zona waktu lokal pada `config/app.php` ke `'timezone' => 'Asia/Jakarta'`.

### Tahap 2: Desain Skema Database & Migrasi
1. Memperbarui migrasi bawaan tabel `users` dengan menambahkan kolom `username`, `role` (default: `'kasir'`), dan `avatar`.
2. Membuat migrasi tabel `menus` untuk menyimpan data produk kuliner, SKU unik, kategori, harga jual, harga modal, dan kuantitas stok.
3. Membuat migrasi tabel `transaksis` sebagai header transaksi keuangan, mencakup kode transaksi, relasi kasir, identitas pemesan, subtotal, pajak, diskon, total, metode bayar, dan nominal kembalian.
4. Membuat migrasi tabel `transaksi_items` untuk rincian baris pembelian dengan snapshot nama dan harga produk.
5. Menjalankan migrasi: `php artisan migrate:fresh`.

### Tahap 3: Pembuatan Model Eloquent & Pemetaan Relasi
1. Mendefinisikan model `User` dengan relasi `hasMany(Transaksi::class)`.
2. Mendefinisikan model `Menu` dengan accessor status stok (`status_stok`) yang mengevaluasi kuantitas terhadap ambang batas, serta relasi `hasMany(TransaksiItem::class)`.
3. Mendefinisikan model `Transaksi` dengan relasi `belongsTo(User::class)` dan `hasMany(TransaksiItem::class)`.
4. Mendefinisikan model `TransaksiItem` dengan relasi `belongsTo(Transaksi::class)` dan `belongsTo(Menu::class)`.

### Tahap 4: Penyusunan Seeder Data Realistis
1. Menyusun `UserSeeder` untuk menyuntikkan kredensial kasir utama: `username: kasir`, `password: kasir123`, `role: kasir`.
2. Menyusun `MenuSeeder` yang mencakup 12+ menu artisan (Kategori: Coffee, Non-Coffee, Makanan, Snack) lengkap dengan foto, SKU, dan stok realistis.
3. Menyusun `TransaksiSeeder` yang menggenerate riwayat transaksi hari ini dan hari-hari sebelumnya untuk menguji kalkulasi grafik analitik.

### Tahap 5: Ekstraksi Konfigurasi Terpusat (`config/pos.php`)
1. Membuat file konfigurasi `config/pos.php` untuk menampung seluruh variabel bisnis.
2. Menggantikan seluruh teks statis (nama toko, alamat, persentase PB1 10%, batas stok 15) di seluruh kode agar merujuk ke fungsi pembantu `config('pos.*')`.

### Tahap 6: Implementasi Controller & Logika Bisnis
1. **`AuthController`**: Menangani otentikasi fleksibel (username atau email), regenerasi session untuk mencegah *session fixation*, dan logout.
2. **`DashboardController`**: Mengagregasikan metrik penjualan harian, menghitung persentase perubahan dibanding hari sebelumnya, menyusun data grafik harian (query agnostik jam), mingguan, dan bulanan, serta daftar menu terlaris.
3. **`TransaksiController`**: Menampilkan antarmuka POS, memvalidasi payload keranjang belanja, membuka *Database Transaction* (`DB::beginTransaction`), memverifikasi kecukupan stok, membuat nomor transaksi otomatis, memotong stok, dan mengembalikan respons JSON.
4. **`MenuController`**: Mengelola siklus hidup produk (CRUD), upload file foto ke disk `public`, fungsi ekspor CSV, dan parser impor berkas CSV dengan *fallback sanitization*.
5. **`LaporanController`**: Menyortir riwayat transaksi secara *strictly descending*, merender pratinjau struk thermal, streaming PDF struk, dan ekspor dokumen laporan.

### Tahap 7: Perancangan Antarmuka Pengguna (Blade & Tailwind CSS)
1. Membangun master layout `resources/views/layouts/app.blade.php` dengan sidebar navigasi bersih khusus kasir, topbar informatif, dan area notifikasi flash.
2. Mendesain tampilan login elegan (`auth/login.blade.php`) dengan palet warna navy dan oranye, dilengkapi toggle visibilitas password.
3. Merancang dashboard analitik dengan kartu metrik ringkas dan wadah canvas Chart.js.
4. Menyusun terminal kasir (`pos/index.blade.php`) dengan grid produk adaptif dan keranjang pesanan melayang (*floating cart column*).
5. Merancang antarmuka manajemen menu dengan badge status warna-warni dan modal dialog formulir.
6. Mendesain halaman laporan dengan layout *two-column split view*.

### Tahap 8: Pengembangan Fitur Interaktif Frontend
1. Menulis script keranjang POS: penambahan item, kalkulasi otomatis pajak PB1 (10%), kalkulasi uang kembalian, dan validasi nilai pembayaran.
2. Mengintegrasikan mekanisme AJAX checkout menggunakan `fetch()` API:
   - Mengirim payload transaksi ke server.
   - Mengurangi angka stok produk di DOM secara instan tanpa reload halaman.
   - Menonaktifkan tombol pesanan jika stok habis ($0$).
   - Menampilkan modal konfirmasi pembayaran dan rincian struk.
3. Membangun fitur *Hold Order* berbasis JavaScript state untuk menahan dan memanggil kembali keranjang belanja pelanggan yang tertunda.
4. Menambahkan script *Cash Flow Switcher* pada dashboard untuk mengubah dataset Chart.js secara dinamis saat tombol tab (Hari Ini / Minggu Ini / Bulan Ini) diklik.

### Tahap 9: Integrasi Library Pihak Ketiga (DomPDF & File System)
1. Memasang package `barryvdh/laravel-dompdf`.
2. Merancang view `laporan/struk_pdf.blade.php` dengan dimensi khusus printer thermal (lebar 58mm / 80mm) untuk pencetakan dokumen PDF yang rapi.
3. Mengonfigurasi symbolic link penyimpanan publik via `php artisan storage:link` untuk aksesibilitas gambar menu.

### Tahap 10: Pembersihan Arsitektur & Refactoring Single-Role
1. Menghapus seluruh tombol demo login (Kasir Demo, Admin Demo) dari halaman login guna memastikan sistem berjalan seperti aplikasi produksi sungguhan.
2. Menghilangkan opsi navigasi pengaturan dan pemisahan admin pada sidebar untuk mengukuhkan arsitektur *Single-Role Cashier*.
3. Mengoreksi sorting laporan penjualan agar mutlak mengutamakan transaksi terbaru (`ORDER BY created_at DESC, id DESC`).

### Tahap 11: Pembuatan Pengujian Otomatis (Automated Testing)
1. Mengonfigurasi `phpunit.xml` dengan basis data SQLite in-memory (`:memory:`) untuk pengujian kilat tanpa merusak database lokal.
2. Memperbaiki query ekstraksi jam di `DashboardController` menggunakan rentang waktu `whereBetween` agar kompatibel di MySQL maupun SQLite.
3. Menyusun 5 berkas pengujian fungsional:
   - `AuthTest.php` (5 test)
   - `DashboardTest.php` (1 test)
   - `TransaksiPosTest.php` (4 test)
   - `MenuTest.php` (6 test)
   - `LaporanTest.php` (5 test)
   - `ExampleTest.php` (1 test)
   - `ExampleTest (Unit)` (1 test)
4. Memastikan seluruh 23 test dan 80 assertions lulus dengan status hijau (100% PASS).

### Tahap 12: Dokumentasi Komprehensif
1. Menyusun file dokumentasi teknis `kopikita.md` sebagai acuan lengkap bagi penguji, pengembang, dan pengguna.

---

## 8. AUTOMATED TESTING & JAMINAN KUALITAS

Aplikasi dilengkapi dengan rangkaian pengujian fitur otomatis (*Automated Feature Testing*) berbasis PHPUnit untuk memastikan seluruh logika bisnis berjalan tanpa cacat:

```
PASS  Tests\Unit\ExampleTest
✓ that true is true

PASS  Tests\Feature\AuthTest
✓ login screen can be rendered
✓ cashier can authenticate using username
✓ cashier can authenticate using email
✓ users cannot authenticate with invalid password
✓ authenticated cashier can logout

PASS  Tests\Feature\DashboardTest
✓ cashier can view dashboard with all metrics

PASS  Tests\Feature\ExampleTest
✓ unauthenticated user redirected to login

PASS  Tests\Feature\LaporanTest
✓ cashier can view sales report
✓ sales report is sorted from newest to oldest
✓ cashier can stream thermal receipt pdf
✓ cashier can export sales report pdf
✓ cashier can export sales report csv

PASS  Tests\Feature\MenuTest
✓ cashier can view menu list
✓ cashier can create new menu
✓ cashier can update menu
✓ cashier can delete menu
✓ cashier can export menu to csv
✓ cashier can import menu from csv

PASS  Tests\Feature\TransaksiPosTest
✓ cashier can view pos terminal
✓ pos checkout via ajax decrements stock and returns json
✓ pos checkout fails when stock is insufficient
✓ pos checkout fails when cash payment is underpaid

Tests:    23 passed (80 assertions)
Duration: 1.34s
```

### Cara Menjalankan Automated Test:
```bash
php artisan test
```

---

## 9. PANDUAN INSTALASI & MENJALANKAN PROYEK

Ikuti langkah-langkah berikut untuk menjalankan sistem di lingkungan lokal:

### 1. Kebutuhan Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `sqlite3`, `mbstring`, `openssl`, `gd`)
- Composer >= 2.x
- MySQL / MariaDB (XAMPP Server)
- Web Browser modern (Google Chrome / Mozilla Firefox / Microsoft Edge)

### 2. Kloning & Dependensi
```bash
# Pindah ke direktori web server
cd C:\xampp\htdocs\kopikita

# Install dependensi PHP
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Pastikan file `.env` sudah sesuai dengan pengaturan server lokal:
```ini
APP_NAME="Kopi Kita"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE="Asia/Jakarta"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kopi_kita
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeder Database
Pastikan layanan Apache dan MySQL pada kontrol panel XAMPP sudah aktif, lalu jalankan:
```bash
# Generate application key (jika belum ada)
php artisan key:generate

# Hubungkan symbolic link storage gambar
php artisan storage:link

# Buat tabel dan suntikkan data awal
php artisan migrate:fresh --seed
```

### 5. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui peramban: `http://127.0.0.1:8000`

### 6. Kredensial Login Kasir
- **URL**: `http://127.0.0.1:8000/login`
- **Username**: `kasir` (atau email: `kasir@kopikita.com`)
- **Password**: `kasir123`

---

## 10. BANK TANYA JAWAB TEKNIS (ASSESSOR Q&A)

Berikut adalah panduan argumen teknis jika tim penguji atau asesor mengajukan pertanyaan saat pengujian:

#### Q1: "Mengapa sistem ini menggunakan Single-Role (`kasir`) bukan Multi-Role (`admin` dan `kasir`)?"
> **Jawaban:**  
> *"Sistem ini dirancang khusus sebagai Point of Sale (POS) ritel kafe yang mengutamakan kecepatan dan kesederhanaan operasional. Pada operasional kafe harian, kasir memegang kendali penuh atas pencatatan order, pemantauan stok, dan pencetakan struk. Menghilangkan distraksi menu admin atau tombol demo memastikan antarmuka tetap bersih, profesional, dan fokus pada efisiensi transaksi di meja kasir."*

#### Q2: "Bagaimana cara sistem mencegah nilai pajak dan nama kafe ter-hardcode di kode program?"
> **Jawaban:**  
> *"Seluruh parameter bisnis diisolasi ke dalam file konfigurasi `config/pos.php` yang dapat di-override melalui file environment `.env`. Controller dan View Blade hanya memanggil helper `config('pos.tax_rate')`, `config('pos.store_name')`, dan `config('pos.low_stock_threshold')`. Jika suatu hari tarif pajak restoran berubah dari 10% menjadi 11%, pengembang cukup mengganti satu baris konfigurasi tanpa perlu membongkar kode controller maupun tampilan."*

#### Q3: "Mengapa tabel `transaksi_items` menyimpan kembali nama menu dan harga, padahal sudah memiliki `menu_id`?"
> **Jawaban:**  
> *"Pola ini dinamakan **Financial Snapshot Pattern**. Dalam sistem keuangan dan POS, data masa lalu bersifat mutlak dan tidak boleh berubah (*immutable*). Jika pemilik kafe menaikkan harga 'Caramel Latte' dari Rp 25.000 menjadi Rp 28.000 di masa depan, total nominal pada struk dan laporan penjualan minggu lalu tidak boleh ikut naik. Snapshot menjamin integritas audit pembukuan historis."*

#### Q4: "Bagaimana alur transaksi POS menangani stok yang tiba-tiba habis saat checkout?"
> **Jawaban:**  
> *"Sistem menerapkan validasi berlapis (Client-Side & Server-Side). Di sisi frontend, item dengan stok 0 otomatis dinonaktifkan. Saat kasir menekan tombol bayar, payload dikirim via AJAX ke `TransaksiController@store`. Di dalam `DB::beginTransaction()`, server memeriksa ketersediaan stok fisik setiap item di tabel `menus`. Jika kuantitas yang diminta melebihi stok yang ada, transaksi otomatis dibatalkan via `DB::rollBack()` dan mengembalikan respons HTTP 422 dengan pesan error yang spesifik."*

#### Q5: "Bagaimana sistem memastikan struk kasir kompatibel dengan printer thermal fisik?"
> **Jawaban:**  
> *"Kami menyediakan dua solusi: pertama, mode cetak langsung via browser yang dilengkapi media query `@media print` untuk menyembunyikan elemen navigasi dan membatasi lebar kertas menjadi 58mm/80mm monokrom. Kedua, rendering sisi server menggunakan `barryvdh/laravel-dompdf` dengan ukuran kertas kustom (`setPaper([0, 0, 226.77, ...])`) yang dapat diunduh langsung sebagai berkas PDF struk resmi."*

#### Q6: "Mengapa grafik jam penjualan di Dashboard tidak menggunakan fungsi `whereRaw('HOUR(created_at) = ?')`?"
> **Jawaban:**  
> *"Fungsi `HOUR()` adalah fungsi spesifik bawaan MySQL/MariaDB yang tidak didukung secara native oleh SQLite yang digunakan pada environment automated testing PHPUnit. Dengan mengubahnya menggunakan `whereBetween('created_at', [$startHour, $endHour])` via Carbon, query menjadi **Database-Agnostic** (kompatibel lintas mesin basis data), memanfaatkan indeks kolom secara optimal, dan memungkinkan automated test berjalan 100% lulus tanpa hambatan."*
