<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$currentUser = getCurrentUser();

// Get asisten data
$asisten_result = $conn->query("
    SELECT a.* FROM asisten_dosen a 
    WHERE a.user_id = " . $currentUser['id']
);
$asisten = $asisten_result ? $asisten_result->fetch_assoc() : null;

// Get praktikum assigned to this asisten (through koordinator)
$praktikum_query = "
    SELECT DISTINCT p.*, mk.nama_mk 
    FROM praktikum p
    LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
    LEFT JOIN koordinator k ON p.koordinator_id = k.id
    LEFT JOIN asisten_dosen a ON a.koordinator_id = k.id
    WHERE 1=1
";
if ($asisten && isset($asisten['id'])) {
    $praktikum_query .= " AND a.id = " . intval($asisten['id']);
} else {
    $praktikum_query .= " AND 1=0"; // Return no results if asisten not found
}
$praktikum_query .= " ORDER BY p.nama_praktikum";
$praktikum = $conn->query($praktikum_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Asisten Dosen - Sistem Informasi Praktikum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Dashboard Asisten Dosen</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($currentUser['nama']); ?></p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Kelola Jadwal dan Presensi</h2>
            </div>
            <div class="card-body">
                <a href="jadwal_praktikum.php" class="btn btn-primary">Atur Jadwal Praktikum</a>
                <a href="presensi.php" class="btn btn-primary">Kelola Presensi</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Praktikum yang Dikelola</h2>
            </div>
            <div class="card-body">
                <?php if ($praktikum->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Praktikum</th>
                                    <th>Mata Kuliah</th>
                                    <th>Semester</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $praktikum->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['nama_praktikum']); ?></td>
                                        <td><?php echo htmlspecialchars($p['nama_mk'] ?? '-'); ?></td>
                                        <td><?php echo $p['semester']; ?></td>
                                        <td><?php echo htmlspecialchars($p['lokasi'] ?? '-'); ?></td>
                                        <td>
                                            <a href="presensi.php?praktikum_id=<?php echo $p['id']; ?>" class="btn btn-info btn-sm">Kelola Presensi</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <p>Belum ada praktikum yang ditugaskan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

