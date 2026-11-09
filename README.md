# ☕ Kopi Kita POS — Cafe & Eatery Point of Sale System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/Tests-23%20Passed-success?style=for-the-badge&logo=phpunit&logoColor=white)](tests)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

Aplikasi kasir (Point of Sale) modern berbasis web yang dirancang khusus untuk operasional kedai kopi dan restoran. Mengusung arsitektur **Single-Role Cashier**, kalkulasi otomatis pajak restoran PB1 (10%), antarmuka asinkron (AJAX) tanpa reload halaman, dan pengujian fitur terotomatisasi (*Automated Testing* 100% lolos).

---

## 🌟 Fitur Utama

- **Single-Role Cashier (`kasir`)**: Alur kerja fokus bebas distraksi menu admin atau tombol demo.
- **Konfigurasi Terpusat (`config/pos.php`)**: Bebas nilai hardcode; pajak PB1, ambang batas stok kritis, dan profil outlet dikendalikan dari satu file konfigurasi.
- **Terminal Kasir POS Asinkron**:
  - Keranjang belanja interaktif dengan kalkulasi subtotal, pajak PB1 10%, dan kembalian instan.
  - Pemotongan stok langsung pada elemen DOM saat checkout tanpa reload.
  - Validasi pembayaran uang tunai di sisi klien dan server.
  - Fitur **Hold Order** untuk menahan dan memanggil kembali transaksi pelanggan yang tertunda.
  - Modal struk belanja otomatis pasca-transaksi dengan opsi cetak dan unduh PDF.
- **Dashboard Analitik Dinamis**:
  - 4 Kartu metrik utama (Omzet hari ini, jumlah transaksi, status stok produk, total porsi terlayani).
  - Grafik *Chart.js* multi-rentang interaktif (Hari Ini per jam, Minggu Ini per hari, Bulan Ini per minggu).
  - Daftar 5 Menu Terlaris dan 5 Transaksi Terkini.
- **Manajemen Menu Lengkap (CRUD)**:
  - Tambah, edit, dan hapus menu dengan unggah foto ke storage lokal.
  - Ekspor katalog menu ke berkas CSV.
  - Impor berkas CSV melalui native file picker dengan mekanisme *upsert* otomatis.
- **Laporan Penjualan & Cetak Struk**:
  - Riwayat transaksi terurut ketat dari yang paling baru ke paling lama (`DESC`).
  - Pratinjau struk thermal interaktif (split-view) saat baris riwayat diklik.
  - Cetak struk printer thermal (58mm/80mm) via browser print CSS.
  - Streaming cetak PDF struk kasir dan ekspor rekapitulasi laporan (PDF & CSV) menggunakan `barryvdh/laravel-dompdf`.
- **100% Automated Feature Testing**: 23 pengujian otomatis mencakup autentikasi, dashboard, POS checkout, menu CRUD, dan laporan menggunakan basis data SQLite in-memory.

---

## 🛠️ Prasyarat Sistem

- **PHP**: >= 8.2 (ekstensi `pdo_mysql`, `sqlite3`, `mbstring`, `openssl`, `gd`)
- **Composer**: >= 2.x
- **Basis Data**: MySQL / MariaDB (XAMPP Server)
- **Web Browser**: Chrome, Firefox, Edge, Safari

---

## 🚀 Panduan Instalasi Lokal

### 1. Kloning Repositori
```bash
git clone https://github.com/roidpalih-coder/kopikita.git
cd kopikita
```

### 2. Instal Dependensi
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Pastikan konfigurasi basis data pada `.env` telah sesuai:
```ini
APP_NAME="Kopi Kita"
APP_ENV=local
APP_TIMEZONE="Asia/Jakarta"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kopi_kita
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Migrasi & Seeder Database
Pastikan MySQL di XAMPP telah aktif dan database `kopi_kita` sudah dibuat:
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server Pengembangan
```bash
php artisan serve
```
Buka peramban dan akses: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Kredensial Login Kasir

| Parameter | Kredensial |
|---|---|
| **Halaman Login** | `http://127.0.0.1:8000/login` |
| **Username** | `kasir` *(atau `kasir@kopikita.com`)* |
| **Password** | `kasir123` |
| **Peran (Role)** | `kasir` |

---

## 🧪 Menjalankan Automated Testing

Sistem dilengkapi automated test suite lengkap yang berjalan di atas SQLite in-memory:
```bash
php artisan test
```

Hasil pengujian:
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\AuthTest
PASS  Tests\Feature\DashboardTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\LaporanTest
PASS  Tests\Feature\MenuTest
PASS  Tests\Feature\TransaksiPosTest

Tests:    23 passed (80 assertions)
Duration: ~1.3s
```

---

## 📂 Struktur Utama Proyek

```
kopikita/
├── app/
│   ├── Http/Controllers/       # AuthController, DashboardController, MenuController, TransaksiController, LaporanController
│   └── Models/                 # User, Menu, Transaksi, TransaksiItem
├── config/
│   └── pos.php                 # Konfigurasi terpusat (metadata kafe, PB1 10%, batas stok 15)
├── database/
│   ├── migrations/             # Skema tabel users, menus, transaksis, transaksi_items
│   └── seeders/                # Data awal kasir, menu katalog, dan transaksi historis
├── resources/views/
│   ├── auth/                   # Form login kasir
│   ├── dashboard/              # Dashboard analitik & switcher Chart.js
│   ├── pos/                    # Terminal kasir, keranjang live, hold order, modal bayar
│   ├── menu/                   # Antarmuka manajemen menu & import/export CSV
│   └── laporan/                # Split-view riwayat transaksi & template struk thermal PDF
├── tests/Feature/              # Automated test suites
└── kopikita.md                 # Dokumentasi arsitektur dan panduan teknis lengkap
```

---

## 📖 Dokumentasi Lengkap

Panduan menyeluruh mengenai arsitektur perangkat lunak, relasi basis data, snapshot pattern, dan panduan ujian kompetensi tersedia di file [kopikita.md](kopikita.md).

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi [MIT](LICENSE).
