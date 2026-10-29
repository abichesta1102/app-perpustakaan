# Skenario Demo Aplikasi Perpustakaan Digital Kampus

Dokumen ini berisi panduan skenario demo aplikasi **app-perpustakaan** untuk pengujian menyeluruh dan persiapan Ujian Akhir Semester (UAS).

---

## I. Persiapan Lingkungan Demo

1. **Jalankan Database MySQL** (melalui Laragon / XAMPP / MySQL Service port 3306).
2. **Reset & Isi Data Dummy Realistis:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Data otomatis terisi: 3 User (1 Admin, 2 Petugas), 5 Kategori, 20 Buku, 15 Anggota, dan 10 Transaksi Peminjaman dengan data kurasi Indonesia.*
3. **Jalankan Dua Instance Web Server (Terminal Berdampingan):**
   - **Terminal 1 (Server Utama - Akses Pengguna/Browser):**
     ```bash
     php artisan serve --port=8000
     ```
   - **Terminal 2 (Server Internal API - Endpoint Konsumsi):**
     ```bash
     php artisan serve --port=8011
     ```

---

## II. Akun Uji Coba (Kredensial)

| Role | Nama | Email | Password |
|---|---|---|---|
| **Admin** | Bambang Sutrisno | `admin@pens.ac.id` | `password` |
| **Petugas 1** | Siti Rahmawati | `petugas1@pens.ac.id` | `password` |
| **Petugas 2** | Ahmad Fauzi | `petugas2@pens.ac.id` | `password` |

---

## III. Langkah-Langkah Skenario Demo UAS

### 1. Autentikasi (Login)
1. Buka browser dan arahkan ke `http://127.0.0.1:8000/login`.
2. Masukkan email `admin@pens.ac.id` dan password `password`.
3. Klik tombol **Login**.
4. **Verifikasi:** Sistem berhasil login dan secara otomatis me-redirect pengguna ke halaman **Dashboard** (`/`), serta menampilkan salam selamat datang dan nama akun **Bambang Sutrisno (Admin)** pada navbar.

### 2. Tampilkan Dashboard & Konsumsi API Statistik
1. Amati tampilan kartu statistik di halaman Dashboard.
2. **Verifikasi:** 
   - Nilai statistik diambil secara dinamis via Laravel HTTP Client dari `GET /api/stats`.
   - Menampilkan 3 kartu ringkasan: **Total Buku (20)**, **Total Anggota (15)**, dan **Peminjaman Aktif**.
   - Terdapat tombol aksi **"Lihat Laporan Peminjaman"** yang mengarah ke `/loans/report`.

### 3. Tambah Kategori Buku (Role Admin)
1. Klik menu **Kategori** pada navbar (`/categories`). *(Menu ini hanya muncul jika login sebagai admin).*
2. Klik tombol **Tambah Kategori**.
3. Masukkan data:
   - Nama Kategori: `Ensiklopedia`
   - Deskripsi: `Buku referensi lengkap memuat berbagai topik ilmiah.`
4. Klik **Simpan**.
5. **Verifikasi:** Kategori baru muncul di tabel kategori dengan alert sukses.

### 4. Tambah Buku Baru
1. Klik menu **Buku** pada navbar (`/books`).
2. Perhatikan pagination di bawah tabel buku tampil rapi (teks/link bersih `« Sebelumnya`, nomor halaman, `Berikutnya »`).
3. Klik tombol **Tambah Buku**.
4. Masukkan data:
   - Judul: `Ensiklopedia Sains Nusantara`
   - Penulis: `Prof. Dr. Bambang Hermanto`
   - Penerbit: `Gramedia Pustaka Utama`
   - Tahun Terbit: `2024`
   - ISBN: `9786020388901`
   - Stok: `5`
   - Kategori: Pilih `Ensiklopedia`
5. Klik **Simpan**.
6. **Verifikasi:** Buku baru berhasil tersimpan dan stok tercatat.

### 5. Tambah Anggota Kampus Baru
1. Klik menu **Anggota** pada navbar (`/members`).
2. Perhatikan format nama dan email anggota bergaya Indonesia dengan domain resmi `@pens.ac.id`.
3. Klik tombol **Tambah Anggota**.
4. Masukkan data:
   - Nama: `Muhammad Rizky Pratama`
   - NIM: `3125600999`
   - Email: `muhammad.rizky999@pens.ac.id`
   - Nomor Telepon: `081234567890`
   - Alamat: `Jl. Raya ITS Kampus PENS, Sukolilo, Surabaya`
   - Status: `Aktif`
5. Klik **Simpan**.
6. **Verifikasi:** Anggota baru tersimpan dalam database.

### 6. Transaksi Peminjaman Buku Baru
1. Klik menu **Peminjaman** pada navbar (`/loans`).
2. Klik tombol **Buat Peminjaman**.
3. Pilih:
   - Anggota: `Muhammad Rizky Pratama`
   - Tanggal Pinjam: Hari ini
   - Tanggal Kembali: 7 hari ke depan
   - Pilih Buku: Centang 1 atau 2 buku (misal `Ensiklopedia Sains Nusantara`).
4. Klik **Simpan Transaksi**.
5. **Verifikasi:** Transaksi berhasil dibuat dengan status `dipinjam`, dan petugas tercatat sebagai `Bambang Sutrisno`.

### 7. Lihat Daftar Peminjaman Aktif & Detail Transaksi
1. Pada tabel peminjaman di `/loans`, cari transaksi yang baru dibuat.
2. Klik tombol **Detail** pada transaksi tersebut.
3. **Verifikasi:** Menampilkan detail peminjam, petugas penanggung jawab, daftar buku yang dipinjam, dan status `dipinjam`.

### 8. Proses Pengembalian Buku
1. Pada baris transaksi peminjaman berstatus `dipinjam`, klik tombol **Kembalikan**.
2. **Verifikasi:** Status transaksi berubah menjadi `dikembalikan` dan tanggal pengembalian otomatis tercatat saat tombol diklik.
3. Kembali ke Dashboard (`/`), verifikasi bahwa angka **Peminjaman Aktif** telah berkurang 1.

### 9. Tampilkan Laporan Peminjaman (Konsumsi API)
1. Klik menu **Laporan** pada navbar atau buka URL `/loans/report`.
2. **Verifikasi:**
   - Halaman menampilkan tabel rekap transaksi dari endpoint internal `GET /api/loans` (bukan query Eloquent langsung).
   - Menampilkan kolom ID, Anggota, Petugas, Buku, Tgl Pinjam, Tgl Kembali, dan Status.
   - Navigasi halaman laporan berbasis meta data API (`meta.current_page` dan `meta.last_page`).

### 10. Uji Coba Ketahanan Koneksi (Connection Fallback)
1. Matikan sementara server kedua di port `8011` (tekan `Ctrl + C` pada Terminal 2).
2. Refresh halaman **Dashboard** (`/`) dan **Laporan** (`/loans/report`).
3. **Verifikasi:**
   - Kedua halaman tetap terbuka tanpa melempar error `500 Server Error`.
   - Dashboard menampilkan fallback angka `0`.
   - Halaman Laporan menampilkan fallback tabel kosong ("Belum ada data peminjaman").
   - Hal ini membuktikan bahwa blok `try/catch (\Illuminate\Http\Client\ConnectionException $e)` berfungsi sempurna.
4. Nyalakan kembali server port `8011` (`php artisan serve --port=8011`) dan refresh, data kembali muncul normal.

### 11. Pengujian REST API lewat Postman / cURL
Buka Postman atau terminal cURL untuk menguji endpoint REST API publik:
- **Statistik Sistem:**
  ```http
  GET http://127.0.0.1:8000/api/stats
  ```
  *Response:* `{"total_buku":21,"total_anggota":16,"peminjaman_aktif":...}`
- **Daftar Buku:**
  ```http
  GET http://127.0.0.1:8000/api/books
  ```
  *Response:* JSON koleksi buku dengan relasi kategori & pagination.
- **Daftar Anggota:**
  ```http
  GET http://127.0.0.1:8000/api/members
  ```
  *Response:* JSON koleksi anggota perpustakaan.
- **Daftar Transaksi Peminjaman:**
  ```http
  GET http://127.0.0.1:8000/api/loans?page=1
  ```
  *Response:* JSON koleksi peminjaman beserta anggota, petugas, dan daftar buku.

---

## IV. Penutup
Seluruh fungsionalitas dari Pertemuan 1 hingga Pertemuan 10 telah terintegrasi penuh:
- Arsitektur MVC & Blade Component
- Validasi Form & Authorization Middleware (Admin & Petugas)
- RESTful API & API Resource
- Client API Consumption (Backend for Frontend pattern)
- Seeder & Factory Kurasi Bahasa Indonesia
- Custom Styled Pagination
