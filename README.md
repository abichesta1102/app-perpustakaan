# 📚 Sistem Perpustakaan Digital Kampus (`app-perpustakaan`)

Aplikasi manajemen perpustakaan berbasis **Laravel 12** yang dibangun secara bertahap melalui rangkaian praktikum. Proyek ini mengimplementasikan konsep arsitektur MVC, autentikasi multi-role (Admin dan Petugas), manajemen data CRUD lengkap, relasi Eloquent Many-to-Many, RESTful API dengan API Resources, serta konsumsi API internal menggunakan Laravel HTTP Client.

---

## 🚀 Fitur Utama

- **Autentikasi & Otorisasi:** Sistem Login/Logout multi-role (Admin dan Petugas) dengan proteksi middleware.
- **Manajemen Buku:** CRUD Buku lengkap dengan relasi Kategori, stok, ISBN, dan penerbit.
- **Manajemen Kategori:** CRUD Kategori buku (khusus role Admin).
- **Manajemen Anggota:** CRUD Anggota mahasiswa kampus lengkap dengan validasi NIM dan email.
- **Sirkulasi Peminjaman:** Transaksi peminjaman banyak buku (*loan items*), pelacakan tanggal kembali, dan proses pengembalian buku.
- **RESTful API:** Endpoint API mandiri untuk `/api/stats`, `/api/books`, `/api/members`, dan `/api/loans` menggunakan Laravel API Resources.
- **Dashboard & Laporan (Konsumsi API):** Dashboard dan Laporan mengonsumsi endpoint API internal secara asynchronous menggunakan Laravel HTTP Client (`Illuminate\Support\Facades\Http`) dengan penanganan fallback koneksi (*graceful fallback*).
- **Data Dummy Realistis:** Seeder & Factory berbasis locale `id_ID` dengan kurasi judul buku dan penerbit nasional serta domain email resmi kampus `@pens.ac.id`.
- **Tampilan Bersih & Responsif:** Layout Blade modular dengan pagination custom yang rapi tanpa ketergantungan framework CSS eksternal.

---

## 🛠️ Prasyarat Sistem

- **PHP:** Versi 8.2 atau lebih baru (dengan ekstensi `pdo_mysql`, `curl`, `mbstring`, `openssl`).
- **Composer:** Versi 2.x
- **Database Server:** MySQL 8.0+ / MariaDB (via Laragon / XAMPP / bawaan sistem).
- **Git**

---

## 📦 Panduan Instalasi & Setup

### 1. Clone Repositori
```bash
git clone https://github.com/abichesta1102/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Di Windows PowerShell: `Copy-Item .env.example .env`)*

Buka file `.env` dan sesuaikan koneksi database serta konfigurasi port:
```env
APP_NAME=Perpustakaan
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Wajib: URL instance kedua khusus panggilan API internal
INTERNAL_API_URL=http://127.0.0.1:8011

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

# Locale Faker Indonesia
APP_FAKER_LOCALE=id_ID
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Migrasi & Seeding Database
Pastikan server MySQL sudah berjalan di port 3306, lalu jalankan:
```bash
php artisan migrate:fresh --seed
```
Perintah ini akan membuat struktur tabel dari awal dan mengisi database dengan:
- **3 User:** 1 Admin (`admin@pens.ac.id`), 2 Petugas (`petugas1@pens.ac.id`, `petugas2@pens.ac.id`).
- **5 Kategori Buku:** Novel, Komik, Sains, Teknologi, Sejarah.
- **20 Buku:** Judul dan penerbit Indonesia.
- **15 Anggota:** Mahasiswa dengan email `@pens.ac.id`.
- **10 Transaksi Peminjaman:** Dilengkapi rincian buku dan status transaksi.

---

## ⚡ Menjalankan Aplikasi (Dua Server)

Server pengembangan bawaan PHP (`php artisan serve`) hanya memproses satu request dalam satu waktu secara default (*single-threaded*). Karena halaman **Dashboard** dan **Laporan** memanggil endpoint API aplikasi sendiri via HTTP Client, menjalankan satu server di port yang sama akan menyebabkan **deadlock**.

Oleh karena itu, buka **dua jendela terminal terpisah**:

### Terminal 1 — Server Utama (Untuk Browser)
```bash
php artisan serve --port=8000
```
Akses aplikasi melalui peramban web di: **`http://127.0.0.1:8000`**

### Terminal 2 — Server Internal API (Untuk Endpoint Konsumsi)
```bash
php artisan serve --port=8011
```
Server ini akan melayani request HTTP internal dari `DashboardController` dan `LoanController@report`.

> **Catatan Ketahanan:** Jika server port 8011 belum menyala atau mati, aplikasi tidak akan crash (bebas dari HTTP 500 error) berkat penanganan exception `\Illuminate\Http\Client\ConnectionException` yang akan menampilkan data default bernilai `0` / tabel kosong secara anggun (*graceful fallback*).

---

## 🔑 Kredensial Akun Bawaan

| Akun | Email | Password | Role | Hak Akses |
|---|---|---|---|---|
| **Admin** | `admin@pens.ac.id` | `password` | `admin` | Akses penuh (Dashboard, Buku, Kategori, Anggota, Peminjaman, Laporan) |
| **Petugas 1** | `petugas1@pens.ac.id` | `password` | `petugas` | Akses operasional (Dashboard, Buku, Anggota, Peminjaman, Laporan) |
| **Petugas 2** | `petugas2@pens.ac.id` | `password` | `petugas` | Akses operasional (Dashboard, Buku, Anggota, Peminjaman, Laporan) |

---

## 🌐 Dokumentasi RESTful API

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/stats` | Ringkasan statistik (total buku, anggota, peminjaman aktif) |
| `GET` | `/api/books` | Mengambil daftar buku (terpaginasi) |
| `GET` | `/api/books/{id}` | Mengambil detail spesifik satu buku |
| `POST` | `/api/books` | Menambahkan buku baru |
| `PUT` | `/api/books/{id}` | Memperbarui data buku |
| `DELETE` | `/api/books/{id}` | Menghapus buku |
| `GET` | `/api/members` | Mengambil daftar anggota (terpaginasi) |
| `GET` | `/api/members/{id}` | Mengambil detail satu anggota |
| `POST` | `/api/members` | Mendaftarkan anggota baru |
| `PUT` | `/api/members/{id}` | Memperbarui data anggota |
| `DELETE` | `/api/members/{id}` | Menghapus data anggota |
| `GET` | `/api/loans` | Mengambil riwayat peminjaman beserta detail relasi |
| `GET` | `/api/loans/{id}` | Mengambil detail transaksi peminjaman |
| `POST` | `/api/loans` | Membuat transaksi peminjaman baru |

---

## 📋 Panduan Skenario Demo UAS

Langkah-langkah terperinci untuk demonstrasi aplikasi, pengujian fitur admin/petugas, proses peminjaman-pengembalian, serta pengujian Postman API tersedia di file:
👉 **[`DEMO.md`](./DEMO.md)**

---

## 🌿 Struktur Percabangan Git (Branching Workflow)

Repositori ini mengikuti alur pengerjaan praktikum terstruktur:
- `main` : Branch stabil production-ready hasil finalisasi.
- `dev` : Branch integrasi pengembangan.
- `pertemuan-1` s/d `pertemuan-10` : Branch catatan progres masing-masing modul praktikum.
