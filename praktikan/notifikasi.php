<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('praktikan');

$currentUser = getCurrentUser();

// Get all notifikasi
$notifikasi = $conn->query("
    SELECT * FROM notifikasi 
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - Praktikan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Notifikasi & Pengumuman</h2>
            </div>
            <div class="card-body">
                <?php if ($notifikasi->num_rows > 0): ?>
                    <?php while ($n = $notifikasi->fetch_assoc()): ?>
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($n['judul']); ?></h3>
                                <p>
                                    <span class="badge badge-<?php echo $n['tipe'] === 'penting' ? 'danger' : ($n['tipe'] === 'jadwal' ? 'warning' : 'info'); ?>">
                                        <?php echo htmlspecialchars($n['tipe']); ?>
                                    </span>
                                    <span style="margin-left: 10px; color: #666;">
                                        <?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="card-body">
                                <p><?php echo nl2br(htmlspecialchars($n['isi'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🔔</div>
                        <p>Belum ada notifikasi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

