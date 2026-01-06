<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('praktikan');

$currentUser = getCurrentUser();

// Get praktikan data
$praktikan = $conn->query("
    SELECT * FROM praktikan 
    WHERE user_id = " . $currentUser['id']
)->fetch_assoc();

// Get notifikasi count
$notif_count = $conn->query("SELECT COUNT(*) as count FROM notifikasi")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Praktikan - Sistem Informasi Praktikum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Dashboard Praktikan</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($currentUser['nama']); ?></p>
        </div>
        
        <div class="menu-grid">
            <a href="praktikum.php" class="menu-card">
                <div class="menu-card-icon">📚</div>
                <h3>Praktikum</h3>
                <p>Lihat jadwal praktikum, status presensi, dan detail praktikum</p>
            </a>
            
            <a href="koordinator.php" class="menu-card">
                <div class="menu-card-icon">👨‍🏫</div>
                <h3>Koordinator</h3>
                <p>Informasi koordinator dan asisten praktikum</p>
            </a>
            
            <a href="profile.php" class="menu-card">
                <div class="menu-card-icon">👤</div>
                <h3>Profile</h3>
                <p>Informasi praktikan (Nama, NIM, Kelas, Angkatan)</p>
            </a>
            
            <a href="notifikasi.php" class="menu-card">
                <div class="menu-card-icon">🔔</div>
                <h3>Notifikasi</h3>
                <p>Pengumuman perubahan jadwal dan informasi penting</p>
                <?php if ($notif_count > 0): ?>
                    <span class="badge badge-danger" style="margin-top: 10px;"><?php echo $notif_count; ?> baru</span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</body>
</html>

