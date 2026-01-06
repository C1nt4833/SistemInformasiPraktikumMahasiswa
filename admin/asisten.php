<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM asisten_dosen WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Asisten berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus asisten!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Get all asisten with related data
$asisten = $conn->query("
    SELECT 
        a.*,
        u.email AS user_email,
        k.nama AS nama_koordinator
    FROM asisten_dosen a
    LEFT JOIN users u ON a.user_id = u.id
    LEFT JOIN koordinator k ON a.koordinator_id = k.id
    ORDER BY a.nama
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Asisten - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Asisten Dosen</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <a href="asisten_add.php" class="btn btn-primary mb-20">Tambah Asisten Baru</a>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Koordinator</th>
                                <th>No. HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($a = $asisten->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $a['id']; ?></td>
                                    <td><?php echo htmlspecialchars($a['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($a['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($a['nama_koordinator'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($a['no_hp'] ?? '-'); ?></td>
                                    <td>
                                        <a href="asisten_edit.php?id=<?php echo $a['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $a['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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

