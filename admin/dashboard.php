<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$currentUser = getCurrentUser();

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'total_praktikan' => $conn->query("SELECT COUNT(*) as count FROM users WHERE role='praktikan'")->fetch_assoc()['count'],
    'total_asisten' => $conn->query("SELECT COUNT(*) as count FROM users WHERE role='asisten_dosen'")->fetch_assoc()['count'],
    'total_praktikum' => $conn->query("SELECT COUNT(*) as count FROM praktikum")->fetch_assoc()['count'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Informasi Praktikum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($currentUser['nama']); ?></p>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Pengguna</h3>
                <div class="stat-value"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Praktikan</h3>
                <div class="stat-value"><?php echo $stats['total_praktikan']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Asisten</h3>
                <div class="stat-value"><?php echo $stats['total_asisten']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Praktikum</h3>
                <div class="stat-value"><?php echo $stats['total_praktikum']; ?></div>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="users.php" class="menu-card">
                <div class="menu-card-icon">👥</div>
                <h3>Kelola Pengguna</h3>
                <p>Manajemen data pengguna sistem</p>
            </a>
            
            <a href="praktikum.php" class="menu-card">
                <div class="menu-card-icon">📚</div>
                <h3>Kelola Praktikum</h3>
                <p>Manajemen data praktikum</p>
            </a>
            
            <a href="mata_kuliah.php" class="menu-card">
                <div class="menu-card-icon">📖</div>
                <h3>Kelola Mata Kuliah</h3>
                <p>Manajemen data mata kuliah</p>
            </a>
            
            <a href="koordinator.php" class="menu-card">
                <div class="menu-card-icon">👨‍🏫</div>
                <h3>Kelola Koordinator</h3>
                <p>Manajemen data koordinator</p>
            </a>

            <a href="pengawas.php" class="menu-card">
                <div class="menu-card-icon">👨‍🏫</div>
                <h3>Kelola Pengawas</h3>
                <p>Manajemen data pengawas</p>
            </a>
            
            <a href="asisten.php" class="menu-card">
                <div class="menu-card-icon">👨‍💼</div>
                <h3>Kelola Asisten</h3>
                <p>Manajemen data asisten dosen</p>
            </a>
            
            <a href="notifikasi.php" class="menu-card">
                <div class="menu-card-icon">🔔</div>
                <h3>Kelola Notifikasi</h3>
                <p>Manajemen pengumuman dan notifikasi</p>
            </a>
        </div>
    </div>
</body>
</html>

