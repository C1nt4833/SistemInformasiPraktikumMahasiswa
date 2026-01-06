# Sistem Informasi Praktikum Mahasiswa

Sistem informasi untuk mengelola praktikum mahasiswa dengan fitur lengkap untuk Admin, Asisten Dosen, dan Praktikan.

## Fitur Utama

### 1. Menu Praktikum (Praktikan)
- Lihat jadwal praktikum (hari ini & minggu ini)
- Status presensi
- Detail praktikum lengkap
- Deadline laporan
- Informasi koordinator asisten
- Lokasi praktikum
- Semester praktikum

### 2. Menu Koordinator (Praktikan)
- Data praktikum
- Informasi mata kuliah praktikum
- Informasi koordinator dan asisten praktikum
- Data asisten

### 3. Menu Profile (Praktikan)
- Informasi praktikan (Nama, NIM, Kelas, Angkatan)

### 4. Menu Notifikasi (Praktikan)
- Pengumuman perubahan jadwal
- Notifikasi penting

### 5. Menu Login
- Login dengan email dan password
- Role-based access (Admin, Asisten Dosen, Praktikan)

## Role dan Akses

### Admin
- Kelola semua pengguna
- Kelola praktikum
- Kelola mata kuliah
- Kelola koordinator
- Kelola asisten dosen
- Kelola notifikasi

### Asisten Dosen
- Kelola presensi praktikum
- Update status presensi mahasiswa

### Praktikan
- Lihat jadwal praktikum
- Lihat status presensi
- Lihat detail praktikum
- Lihat informasi koordinator dan asisten
- Lihat profile
- Lihat notifikasi

## Instalasi

### 1. Persyaratan
- XAMPP atau Laragon
- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Web browser (Chrome, Firefox, Edge)

### 2. Langkah Instalasi

1. **Copy folder proyek** ke direktori web server:
   - XAMPP: `C:\xampp\htdocs\`
   - Laragon: `C:\laragon\www\`

2. **Buat database**:
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Import file `database/praktikum_db.sql`
   - Atau jalankan SQL file melalui phpMyAdmin

3. **Konfigurasi database** (jika perlu):
   - Edit file `config/database.php`
   - Sesuaikan DB_HOST, DB_USER, DB_PASS, DB_NAME jika berbeda

4. **Akses aplikasi**:
   - Buka browser
   - Akses: `http://localhost/MPTI/` (sesuaikan dengan nama folder)

## Default Login

Setelah import database, gunakan kredensial berikut:

### Admin
- Email: `admin@praktikum.com`
- Password: `password`

### Asisten Dosen
- Email: `asisten1@praktikum.com`
- Password: `password`
- Email: `asisten2@praktikum.com`
- Password: `password`

### Praktikan
- Email: `mahasiswa1@praktikum.com`
- Password: `password`
- Email: `mahasiswa2@praktikum.com`
- Password: `password`

## Struktur Folder

```
MPTI/
├── admin/              # Halaman admin
│   ├── dashboard.php
│   ├── users.php
│   ├── praktikum.php
│   ├── mata_kuliah.php
│   ├── koordinator.php
│   ├── asisten.php
│   └── notifikasi.php
├── asisten/            # Halaman asisten dosen
│   ├── dashboard.php
│   └── presensi.php
├── praktikan/          # Halaman praktikan
│   ├── dashboard.php
│   ├── praktikum.php
│   ├── koordinator.php
│   ├── profile.php
│   └── notifikasi.php
├── config/             # Konfigurasi
│   ├── database.php
│   └── session.php
├── includes/           # File include
│   └── navbar.php
├── assets/             # CSS, JS, images
│   └── css/
│       └── style.css
├── database/           # File SQL
│   └── praktikum_db.sql
├── index.php           # Halaman login
├── logout.php          # Logout handler
└── README.md           # Dokumentasi
```

## Warna Tema

Sistem menggunakan tema profesional dengan warna:
- **Navy Blue** (#001f3f) - Warna utama
- **Black** (#000000) - Teks
- **White** (#ffffff) - Background

## Fitur Tambahan

- Responsive design (mobile-friendly)
- Form validation
- Session management
- Role-based access control
- CRUD operations untuk semua data
- Search dan filter

## Catatan Penting

1. Pastikan MySQL/MariaDB service berjalan
2. Pastikan PHP extension mysqli aktif
3. Pastikan folder memiliki permission yang tepat
4. Untuk production, ubah password default dan aktifkan HTTPS

## Troubleshooting

### Database connection error
- Pastikan MySQL service berjalan
- Cek konfigurasi di `config/database.php`
- Pastikan database sudah dibuat dan diimport

### Session error
- Pastikan session folder writable
- Cek PHP session configuration

### Page not found
- Pastikan URL path sesuai dengan struktur folder
- Cek .htaccess jika menggunakan Apache

## Pengembangan

Untuk menambahkan fitur baru:
1. Buat file PHP di folder sesuai role
2. Tambahkan query database sesuai kebutuhan
3. Update CSS jika perlu styling baru
4. Test di localhost sebelum deploy

## Lisensi

Proyek ini dibuat untuk keperluan akademik.

## Support

Untuk pertanyaan atau masalah, silakan hubungi administrator sistem.

