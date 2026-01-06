-- Database: praktikum_db
-- Sistem Informasi Praktikum Mahasiswa

CREATE DATABASE IF NOT EXISTS praktikum_db;
USE praktikum_db;

-- Table: users (untuk login semua role)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nim VARCHAR(20),
    role ENUM('admin', 'asisten_dosen', 'praktikan') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: praktikan (detail praktikan)
CREATE TABLE IF NOT EXISTS praktikan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nim VARCHAR(20) UNIQUE NOT NULL,
    kelas VARCHAR(50),
    angkatan YEAR,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mata_kuliah
CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mk VARCHAR(20) UNIQUE NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    semester INT NOT NULL,
    sks INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: koordinator
CREATE TABLE IF NOT EXISTS koordinator (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: asisten_dosen
CREATE TABLE IF NOT EXISTS asisten_dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    koordinator_id INT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (koordinator_id) REFERENCES koordinator(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: praktikum
CREATE TABLE IF NOT EXISTS praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT NOT NULL,
    koordinator_id INT NOT NULL,
    nama_praktikum VARCHAR(100) NOT NULL,
    semester INT NOT NULL,
    lokasi VARCHAR(100),
    deskripsi TEXT,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    FOREIGN KEY (koordinator_id) REFERENCES koordinator(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: jadwal_praktikum
CREATE TABLE IF NOT EXISTS jadwal_praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    praktikum_id INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    tanggal DATE,
    FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: presensi
CREATE TABLE IF NOT EXISTS presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    praktikan_id INT NOT NULL,
    jadwal_praktikum_id INT NOT NULL,
    status ENUM('hadir', 'tidak_hadir', 'izin', 'sakit') DEFAULT 'tidak_hadir',
    waktu_presensi TIMESTAMP NULL,
    asisten_dosen_id INT,
    keterangan TEXT,
    FOREIGN KEY (praktikan_id) REFERENCES praktikan(id) ON DELETE CASCADE,
    FOREIGN KEY (jadwal_praktikum_id) REFERENCES jadwal_praktikum(id) ON DELETE CASCADE,
    FOREIGN KEY (asisten_dosen_id) REFERENCES asisten_dosen(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pengawas_lab
CREATE TABLE IF NOT EXISTS pengawas_lab (
    id_pengawas INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_pengawas VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    shift_pengawasan ENUM('Pagi', 'Siang', 'Sore'),
    tanggal_mulai DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: deadline_laporan
CREATE TABLE IF NOT EXISTS deadline_laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    praktikum_id INT NOT NULL,
    judul_laporan VARCHAR(200) NOT NULL,
    deadline DATETIME NOT NULL,
    deskripsi TEXT,
    FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notifikasi
CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    tipe ENUM('jadwal', 'umum', 'penting') DEFAULT 'umum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: praktikan_praktikum (many-to-many relationship)
CREATE TABLE IF NOT EXISTS praktikan_praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    praktikan_id INT NOT NULL,
    praktikum_id INT NOT NULL,
    FOREIGN KEY (praktikan_id) REFERENCES praktikan(id) ON DELETE CASCADE,
    FOREIGN KEY (praktikum_id) REFERENCES praktikum(id) ON DELETE CASCADE,
    UNIQUE KEY unique_praktikan_praktikum (praktikan_id, praktikum_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
-- Admin
INSERT INTO users (nama, email, password, role) VALUES 
('Admin Sistem', 'admin@praktikum.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password: password

-- Asisten Dosen
INSERT INTO users (nama, email, password, nim, role) VALUES 
('Asisten Dosen 1', 'asisten1@praktikum.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'AS001', 'asisten_dosen'),
('Asisten Dosen 2', 'asisten2@praktikum.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'AS002', 'asisten_dosen');

-- Praktikan
INSERT INTO users (nama, email, password, nim, role) VALUES 
('Mahasiswa 1', 'mahasiswa1@praktikum.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2021001', 'praktikan'),
('Mahasiswa 2', 'mahasiswa2@praktikum.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2021002', 'praktikan');

-- Detail Praktikan
INSERT INTO praktikan (user_id, nim, kelas, angkatan) VALUES 
(3, '2021001', 'A', 2021),
(4, '2021002', 'B', 2021);

-- Mata Kuliah
INSERT INTO mata_kuliah (kode_mk, nama_mk, semester, sks) VALUES 
('MK001', 'Praktikum Pemrograman Web', 3, 2),
('MK002', 'Praktikum Basis Data', 4, 2),
('MK003', 'Praktikum Jaringan Komputer', 5, 2);

-- Koordinator
INSERT INTO koordinator (user_id, nama, email, no_hp) VALUES 
(NULL, 'Dr. Koordinator 1', 'koordinator1@praktikum.com', '081234567890');

-- Asisten Dosen
INSERT INTO asisten_dosen (user_id, koordinator_id, nama, email, no_hp) VALUES 
(2, 1, 'Asisten Dosen 1', 'asisten1@praktikum.com', '081234567891'),
(3, 1, 'Asisten Dosen 2', 'asisten2@praktikum.com', '081234567892');

-- Praktikum
INSERT INTO praktikum (mata_kuliah_id, koordinator_id, nama_praktikum, semester, lokasi, deskripsi) VALUES 
(1, 1, 'Praktikum Pemrograman Web - HTML & CSS', 3, 'Lab Komputer 1', 'Praktikum dasar pemrograman web menggunakan HTML dan CSS'),
(2, 1, 'Praktikum Basis Data - MySQL', 4, 'Lab Komputer 2', 'Praktikum manajemen basis data menggunakan MySQL');

-- Jadwal Praktikum
INSERT INTO jadwal_praktikum (praktikum_id, hari, waktu_mulai, waktu_selesai, tanggal) VALUES 
(1, 'Senin', '08:00:00', '10:00:00', '2024-01-15'),
(1, 'Senin', '10:00:00', '12:00:00', '2024-01-22'),
(2, 'Rabu', '13:00:00', '15:00:00', '2024-01-17'),
(2, 'Rabu', '13:00:00', '15:00:00', '2024-01-24');

-- Deadline Laporan
INSERT INTO deadline_laporan (praktikum_id, judul_laporan, deadline, deskripsi) VALUES 
(1, 'Laporan Praktikum 1 - HTML & CSS', '2024-01-25 23:59:59', 'Laporan praktikum pertama tentang HTML dan CSS'),
(2, 'Laporan Praktikum 1 - MySQL', '2024-01-30 23:59:59', 'Laporan praktikum pertama tentang MySQL');

-- Notifikasi
INSERT INTO notifikasi (judul, isi, tipe) VALUES 
('Perubahan Jadwal Praktikum', 'Jadwal praktikum hari Senin dipindahkan ke hari Selasa pada jam yang sama', 'jadwal'),
('Pengumuman Umum', 'Selamat datang di Sistem Informasi Praktikum', 'umum');

-- Praktikan Praktikum
INSERT INTO praktikan_praktikum (praktikan_id, praktikum_id) VALUES 
(1, 1),
(1, 2),
(2, 1);

