<div align="center">

# PT Pakuan Karya Mandiri
## Sistem Informasi Manajemen Aset & Logistik (SIMAL)

![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)
![Filament 5](https://img.shields.io/badge/Filament-5.x-FDAE4B?logo=filament&logoColor=111827)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8%2B-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-Proprietary-red)

**Sistem Terintegrasi untuk Tata Kelola Aset Fisik, Logistik ATK, Peminjaman Fasilitas, dan Kepatuhan Akuntansi Perusahaan.**

</div>

> **Catatan versi:** proyek ini menggunakan Filament **5.x** (`filament/filament ^5.11`) dan Tailwind CSS **4.x**, bukan Filament v3. Badge mengikuti dependency yang benar-benar digunakan oleh repositori.

---

## Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Fitur Unggulan](#fitur-unggulan)
- [Arsitektur Singkat](#arsitektur-singkat)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi Cepat](#instalasi-cepat)
- [Konvensi Delimiter Import](#konvensi-delimiter-import-excelcsv)
- [Pengujian](#pengujian)
- [Struktur Tim dan Hak Cipta](#struktur-tim-dan-hak-cipta)

## Gambaran Umum

**SIMAL** (Sistem Informasi Manajemen Aset & Logistik) dikembangkan untuk membantu PT Pakuan Karya Mandiri mengelola siklus hidup aset kantor dan kebutuhan logistik secara terpusat. Sistem ini mengurangi pencatatan manual yang tersebar, memudahkan pelacakan barang, dan menyediakan riwayat operasional serta dokumen inventaris yang dapat dicetak.

Aplikasi dibangun dengan Laravel dan panel administrasi Filament. Fitur dikelompokkan secara modular berdasarkan proses kerja—manajemen aset, peminjaman, logistik, pemeliharaan, master data, serta dokumen korporat—dengan relasi data yang konsisten dan antarmuka yang responsif.

## Fitur Unggulan

### 🏢 Manajemen Aset Kantor

- Formulir aset bertahap (*multi-step wizard*) yang membagi input menjadi empat langkah: informasi utama, status & PIC, akuntansi & pajak, serta foto & pemeliharaan.
- Kode aset otomatis dengan pola **`PKM-AST-YYYY-XXXX`**.
- Pembuatan dan pencetakan label aset berisi QR code, termasuk pemindaian kode aset melalui kamera.
- Pencatatan kondisi, kategori, ruangan, foto, PIC/pemegang, kontak, garansi, dan jadwal servis.
- Golongan fiskal **Golongan 1** (masa manfaat bawaan 4 tahun), **Golongan 2** (8 tahun), dan opsi non-depresiasi; sistem menghitung estimasi penyusutan garis lurus dan nilai buku berdasarkan data aset.
- Pencetakan dokumen **BAST** (Berita Acara Serah Terima) untuk aset.

> Perhitungan masa manfaat dan nilai buku merupakan fasilitas pencatatan sistem. Pastikan perlakuan pajak dan pelaporan akuntansi mengikuti kebijakan perusahaan serta arahan tenaga profesional yang berwenang.

### 📦 Logistik & Perlengkapan ATK

- Pelacakan stok perlengkapan kantor dan batas minimum stok (*safety stock*).
- Mutasi barang masuk dan keluar dengan catatan supplier/penerima, jumlah, tanggal, dan ruangan tujuan.
- Kode perlengkapan ATK otomatis dengan pola **`PKM-ATK-XXXX`**.
- Impor massal CSV/XLSX dan ekspor data untuk membantu pemutakhiran inventaris.
- Template CSV menggunakan delimiter titik koma agar kolom terbaca terpisah di Microsoft Excel regional Indonesia.

### 🔄 Peminjaman Fasilitas

- Validasi peminjaman: aset bersama atau aset yang tersedia di gudang dapat dipinjam; aset yang ditetapkan kepada PIC pribadi tidak tersedia untuk peminjaman umum.
- Status aset disinkronkan dengan proses peminjaman dan pengembalian.
- Tab untuk memantau **Sedang Dipinjam**, **Terlambat**, **Riwayat Pengembalian**, dan **Semua**.
- Pencatatan tanggal pinjam, rencana kembali, kondisi, dan konfirmasi pengembalian.

### 🛠️ Pemeliharaan & Jadwal Servis

- Pengingat database untuk aset yang sudah jatuh tempo servis atau pemeriksaan rutin.
- Tab **Perlu Servis & Perbaikan** untuk melihat aset yang jadwalnya dekat maupun aset berkondisi rusak.
- Pencatatan vendor/teknisi, tanggal servis, biaya, rincian pekerjaan, kondisi setelah servis, dan jadwal servis berikutnya.
- Riwayat dan akumulasi biaya servis ditampilkan pada detail aset.

### 🧾 Pelepasan Aset (*Disposal*)

- Pencatatan pelepasan aset yang tidak layak digunakan atau dihapus dari inventaris.
- Metode pelepasan: **Dimusnahkan**, **Dijual / Dilelang**, atau **Dihibahkan**.
- Penyimpanan alasan teknis, nilai penjualan, tanggal, dan pejabat penyetuju.
- Status aset diperbarui menjadi **Rusak/Afkir** dan data pelepasan menjadi dasar cetak **BAP** (Berita Acara Penghapusan).

### 🗂️ Master Data & Ruangan

- Pengelolaan kategori barang dan ruangan kantor.
- Relasi aset dengan kategori dan ruangan untuk memudahkan pencarian serta pelaporan.
- Cetak **Kartu Inventaris Ruangan (KIR)** format A4 berisi daftar aset, kondisi, status, dan PIC; siap ditandatangani dan ditempel di ruangan.

### 📊 Dashboard Eksekutif & In-App SOP

- Ringkasan metrik inventaris dan grafik kondisi aset.
- Tabel ringkas riwayat mutasi ATK terkini.
- Panduan operasional/SOP interaktif di dalam aplikasi, dengan tampilan yang dapat dicetak atau disimpan sebagai PDF melalui browser.

## Arsitektur Singkat

| Lapisan | Teknologi / Peran |
|---|---|
| Aplikasi web | Laravel 12, PHP 8.2+ |
| Panel administrasi | Filament 5 |
| UI dan asset frontend | Tailwind CSS 4, Vite |
| Penyimpanan data | MySQL/MariaDB (SQLite tersedia untuk pengujian) |
| Model domain | Aset, peminjaman, servis, pelepasan, kategori, ruangan, perlengkapan, dan mutasi stok |
| Impor/ekspor | CSV dan XLSX; OpenSpout untuk pembacaan spreadsheet |

## Persyaratan Sistem

- **PHP 8.2 atau lebih baru**, dengan ekstensi:
  - `intl`
  - `pdo_mysql`
  - `gd`
  - Ekstensi Laravel umum seperti `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, dan `bcmath`.
- **Composer 2.x**
- **MySQL atau MariaDB** (XAMPP kompatibel)
- **Node.js dan NPM**
- Web server Apache/Nginx untuk deployment; `php artisan serve` dapat digunakan untuk pengembangan lokal.

## Instalasi Cepat

### 1. Clone repositori

```bash
git clone https://github.com/Ranggaaaxd/aset-inventory-pkm.git
cd aset-inventory-pkm
```

### 2. Pasang dependency PHP dan frontend

```bash
composer install
npm install
```

### 3. Siapkan file environment dan application key

Linux/macOS/Git Bash:

```bash
cp .env.example .env
php artisan key:generate
```

PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 4. Konfigurasikan database

Buat database kosong, misalnya `aset_inventory_pkm`, lalu sesuaikan bagian berikut pada `.env`:

```dotenv
APP_NAME="SIMAL - PT Pakuan Karya Mandiri"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aset_inventory_pkm
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan nama database, username, dan password dengan konfigurasi MySQL setempat. Untuk server produksi, gunakan `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, serta kredensial database khusus yang aman.

### 5. Jalankan migrasi dan data contoh

```bash
php artisan migrate --seed
```

Seeder menyiapkan contoh ruangan, kategori, aset, dan perlengkapan. **Seeder tidak membuat akun pengguna.**

### 6. Buat akun administrator

```bash
php artisan make:filament-user
```

Ikuti prompt untuk mengisi nama, email, dan password. Role akun baru menggunakan nilai default **`admin`**. Setelah masuk, akun dapat dikelola melalui menu **Kelola Pengguna**.

### 7. Siapkan penyimpanan file dan asset frontend

```bash
php artisan storage:link
npm run build
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Buka **[http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)**, lalu masuk dengan akun administrator yang dibuat sebelumnya.

## Konvensi Delimiter Import Excel/CSV

Template impor Aset dan Perlengkapan diunduh dalam format CSV dengan ketentuan:

- Delimiter kolom **titik koma (`;`)** agar sesuai dengan pengaturan regional Indonesia di Microsoft Excel.
- Encoding **UTF-8 dengan BOM**, sehingga karakter dan header dikenali dengan baik.
- Baris pertama berisi nama kolom; baris berikutnya berisi data.
- Nama kategori/ruangan pada file harus cocok dengan data master yang sudah terdaftar.
- File CSV dan XLSX dapat diunggah melalui action **Impor** pada tabel terkait.

Sebaiknya unduh template terbaru dari aplikasi sebelum menyiapkan impor, dan uji file dalam jumlah kecil terlebih dahulu.

## Pengujian

Jalankan seluruh pengujian Laravel:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer test
```

## Struktur Tim dan Hak Cipta

| Peran | Informasi |
|---|---|
| Pengembang | Rangga Febrian |
| Perusahaan | PT Pakuan Karya Mandiri |
| Hak cipta | © PT Pakuan Karya Mandiri. All Rights Reserved. |

### Lisensi

Perangkat lunak ini bersifat **proprietary** dan ditujukan untuk penggunaan PT Pakuan Karya Mandiri. Hak penggunaan, penyalinan, modifikasi, dan distribusi tidak diberikan kecuali atas persetujuan tertulis dari perusahaan.
