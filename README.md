# Staffly — Employee Record Management System

Staffly adalah aplikasi berbasis web untuk pengelolaan data pegawai (*Employee Management System*) yang dirancang dengan arsitektur terstruktur, aman, responsif, dan mudah dipelihara. Proyek ini dibangun sesuai dengan standar kompetensi kerja **Junior Web Developer (JWD)** berbasis SKKNI.

---

## 🚀 Fitur Utama

1. **Autentikasi & Keamanan Sesi**:
   - Sistem login terproteksi menggunakan enkripsi satu arah `password_hash()` dan `password_verify()` (BCRYPT).
   - Validasi sesi (`requireAuth`) dan regenerasi ID sesi (`session_regenerate_id`) untuk mencegah *session hijacking* dan *unauthorized access*.
   - Sanitasi data masukan dan proteksi terhadap XSS menggunakan `htmlspecialchars` dengan encoding UTF-8.

2. **Dashboard Interaktif & Statistik**:
   - Ringkasan metrik total pegawai, total divisi/jabatan aktif, dan nomor kontak terverifikasi.
   - Grafik distribusi jabatan pegawai berbasis **Chart.js**.
   - Daftar pegawai terbaru dengan tautan cepat ke detail profil.

3. **Pengelolaan Data Pegawai (CRUD Lengkap)**:
   - **Create**: Formulir penambahan pegawai baru dengan auto-generate NIP cerdas serta opsi upload foto profil (`.jpg`, `.jpeg`, `.png`, `.webp`).
   - **Read**: Tabel daftar pegawai lengkap dengan avatar/inisial nama, jabatan, email, nomor telepon, dan tanggal bergabung.
   - **Detail**: Halaman detail profil pegawai yang rapi dan terstruktur.
   - **Update**: Edit data pegawai yang sudah ada beserta penggantian foto profil.
   - **Delete (Asynchronous AJAX)**: Penghapusan pegawai dengan konfirmasi Bootstrap Modal dan eksekusi jQuery AJAX tanpa perlu memuat ulang seluruh halaman (*no page reload*).

4. **Pencarian Real-Time (Live Search)**:
   - Filter pencarian data pegawai langsung di sisi klien (*client-side JavaScript*) yang responsif.

5. **Laporan & Ekspor Data**:
   - Ekspor data pegawai ke format **CSV** lengkap dengan penanganan UTF-8 BOM untuk kompatibilitas Microsoft Excel.
   - Fitur cetak laporan langsung (*Print / PDF Export*) yang terintegrasi.

---

## 🛠️ Teknologi & Pustaka (Tech Stack)

| Kategori | Teknologi / Pustaka |
| :--- | :--- |
| **Backend** | PHP 8.x (Native dengan Pemrograman Terstruktur & PDO) |
| **Database** | MySQL / MariaDB (Prepared Statements & Parameterized Queries) |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), jQuery 3.7.1 |
| **UI Framework** | Bootstrap 5.3.3 & Bootstrap Icons 1.11.3 |
| **Data Visualization** | Chart.js 4.4.3 |
| **Server Environment**| Apache (XAMPP / Standalone) |

---

## 📁 Struktur Direktori

```text
staffly/
├── assets/
│   ├── css/
│   │   └── style.css            # Custom CSS dan styling tema modern dark & neon
│   ├── images/
│   │   ├── employees/           # Folder penyimpanan foto unggahan pegawai
│   │   ├── banner-dashboard.jpg # Aset visual dashboard
│   │   ├── banner-hero.jpg      # Aset visual halaman login
│   │   └── logo.png             # Logo resmi aplikasi Staffly
│   └── js/
│       └── script.js            # Validasi form, live search, AJAX delete, toast
├── config/
│   └── koneksi.php              # Inisialisasi PDO, session, helper keamanan & fungsi umum
├── pegawai/
│   ├── index.php                # Tampilan utama tabel data pegawai (Read)
│   ├── tambah.php               # Form penambahan data pegawai (Create)
│   ├── edit.php                 # Form perubahan data pegawai (Update)
│   └── detail.php               # Halaman rincian data pegawai
├── proses/
│   ├── export_csv.php           # Skrip generator ekspor CSV
│   ├── proses_edit.php          # Handler pemrosesan update data & file upload
│   ├── proses_hapus.php         # Handler pemrosesan hapus data via JSON API (AJAX)
│   ├── proses_login.php         # Handler verifikasi kredensial login
│   └── proses_tambah.php        # Handler pemrosesan tambah data & file upload
├── dashboard.php                # Halaman dashboard metrik & grafik Chart.js
├── database.sql                 # Skrip skema database & data inisial
├── index.php                    # Entry point pengarah (redirect) ke login.php
├── login.php                    # Halaman antarmuka login
├── logout.php                   # Pembersihan sesi dan logout pengguna
└── README.md                    # Dokumentasi teknis proyek
```

---

## ⚙️ Panduan Instalasi & Menjalankan Aplikasi

1. **Clone atau Letakkan Proyek**:
   Pastikan folder `staffly` berada di direktori web root server lokal Anda:
   - Pada XAMPP: `C:\xampp\htdocs\staffly`

2. **Jalankan Apache & MySQL**:
   Buka **XAMPP Control Panel**, lalu aktifkan modul **Apache** dan **MySQL**.

3. **Impor Basis Data**:
   - Buka browser dan akses `http://localhost/phpmyadmin`.
   - Buat database baru atau langsung buka tab **Import**.
   - Pilih berkas `database.sql` dari direktori `staffly/database.sql`, lalu klik **Import / Go**.
   - Skrip ini akan membuat database `db_pegawai`, tabel `users`, tabel `employees`, beserta data awal default.

4. **Konfigurasi Koneksi (Opsional)**:
   Jika konfigurasi database Anda berbeda dari default XAMPP, sesuaikan pengaturan pada berkas `config/koneksi.php`:
   ```php
   $host   = '127.0.0.1';
   $dbName = 'db_pegawai';
   $dbUser = 'root';
   $dbPass = ''; // Kosongkan untuk default XAMPP
   ```

5. **Akses Aplikasi**:
   Buka peramban (browser) dan buka alamat:
   ```text
   http://localhost/staffly
   ```

---

## 🔐 Kredensial Login Default

Gunakan kredensial berikut untuk masuk ke dalam sistem:

- **Username**: `admin`
- **Password**: `admin123`

---

## 📋 Pemenuhan Unit Standar Kompetensi (JWD)

Aplikasi Staffly dirancang untuk memenuhi Unit Kompetensi Kerja Nasional Indonesia:
1. **J.620100.005.02** — *Mengimplementasikan User Interface*: Desain responsif, tipografi rapi, komponen Bootstrap, dan feedback interaktif.
2. **J.620100.010.01** — *Menerapkan Perintah Eksekusi Bahasa Pemrograman Berbasis Text, Grafik, dan Multimedia*: Penggunaan manipulasi DOM, grafik Chart.js, dan pemrosesan media/foto.
3. **J.620100.015.01** — *Menyusun Fungsi, File atau Sumber Daya Pemrograman dalam Organisasi yang Rapih*: Pemisahan modular antara logic (`proses/`), konfigurasi (`config/`), tampilan (`pegawai/`), dan aset (`assets/`).
4. **J.620100.016.01** — *Menulis Kode dengan Prinsip Sesuai Guidelines dan Best Practices*: Penamaan variabel deskriptif, indentasi konsisten, penanganan error, dan sanitasi keamanan.
5. **J.620100.017.02** — *Mengimplementasikan Pemrograman Terstruktur*: Modularisasi fungsi, *separation of concerns*, dan pemanfaatan PHP PDO.
6. **J.620100.019.02** — *Menggunakan Library atau Komponen Pre-Existing*: Integrasi Bootstrap 5, Bootstrap Icons, jQuery, dan Chart.js secara efektif.

---

## 📄 Lisensi

Dikembangkan untuk keperluan sertifikasi kompetensi kejuruan Junior Web Developer (JWD). Bebas digunakan dan dikembangkan lebih lanjut untuk keperluan edukasi dan profesional.

