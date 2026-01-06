<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Praktikum berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus praktikum!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Get all praktikum with related data
$praktikum = $conn->query("
    SELECT p.*, mk.nama_mk, k.nama as nama_koordinator 
    FROM praktikum p
    LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
    LEFT JOIN koordinator k ON p.koordinator_id = k.id
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Praktikum - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Praktikum</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <a href="praktikum_add.php" class="btn btn-primary mb-20">Tambah Praktikum Baru</a>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Praktikum</th>
                                <th>Mata Kuliah</th>
                                <th>Koordinator</th>
                                <th>Semester</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $praktikum->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['nama_praktikum']); ?></td>
                                    <td><?php echo htmlspecialchars($p['nama_mk'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($p['nama_koordinator'] ?? '-'); ?></td>
                                    <td><?php echo $p['semester']; ?></td>
                                    <td><?php echo htmlspecialchars($p['lokasi'] ?? '-'); ?></td>
                                    <td>
                                        <a href="praktikum_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

