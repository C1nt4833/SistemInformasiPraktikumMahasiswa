# Panduan Instalasi Sistem Informasi Praktikum

## Langkah-langkah Instalasi

### 1. Persiapan
- Pastikan XAMPP atau Laragon sudah terinstall
- Pastikan Apache dan MySQL service berjalan

### 2. Copy File
- Copy seluruh folder `MPTI` ke:
  - **XAMPP**: `C:\xampp\htdocs\MPTI\`
  - **Laragon**: `C:\laragon\www\MPTI\`

### 3. Import Database
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik tab **Import**
3. Pilih file: `database/praktikum_db.sql`
4. Klik **Go** atau **Import**
5. Pastikan database `praktikum_db` sudah terbuat

### 4. Konfigurasi Database (Opsional)
Jika menggunakan konfigurasi database berbeda, edit file:
```
config/database.php
```

Ubah jika perlu:
- `DB_HOST`: localhost (default)
- `DB_USER`: root (default)
- `DB_PASS`: '' (kosong untuk default)
- `DB_NAME`: praktikum_db

### 5. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/MPTI/
```

## Login Default

Setelah import database, gunakan kredensial berikut:

### Admin
- **Email**: admin@praktikum.com
- **Password**: password

### Asisten Dosen
- **Email**: asisten1@praktikum.com
- **Password**: password
- **Email**: asisten2@praktikum.com
- **Password**: password

### Praktikan
- **Email**: mahasiswa1@praktikum.com
- **Password**: password
- **Email**: mahasiswa2@praktikum.com
- **Password**: password

## Troubleshooting

### Error: Database connection failed
**Solusi:**
1. Pastikan MySQL service berjalan
2. Cek konfigurasi di `config/database.php`
3. Pastikan database sudah diimport

### Error: Access denied
**Solusi:**
1. Pastikan user MySQL memiliki akses
2. Cek password di `config/database.php`

### Halaman tidak muncul / 404 Error
**Solusi:**
1. Pastikan folder berada di lokasi yang benar
2. Pastikan Apache service berjalan
3. Cek URL path di browser

### Session error
**Solusi:**
1. Pastikan folder `tmp` atau session folder writable
2. Cek PHP session configuration di php.ini

## Catatan Penting

1. **Password Default**: Segera ubah password default setelah instalasi
2. **Data Sample**: Database sudah berisi data sample untuk testing
3. **Production**: Untuk production, aktifkan HTTPS dan ubah semua password

## Struktur File Penting

```
MPTI/
├── index.php              # Halaman login
├── config/
│   ├── database.php      # Konfigurasi database
│   └── session.php       # Session management
├── admin/                # Halaman admin
├── asisten/              # Halaman asisten dosen
├── praktikan/            # Halaman praktikan
├── database/
│   └── praktikum_db.sql  # File SQL untuk import
└── assets/
    └── css/
        └── style.css     # Styling aplikasi
```

## Support

Jika mengalami masalah, pastikan:
1. PHP version >= 7.4
2. MySQL/MariaDB berjalan
3. Extension mysqli aktif
4. Semua file dan folder permission sudah benar

