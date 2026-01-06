<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM koordinator WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Koordinator berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus koordinator!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Get all koordinator with user info
$koordinator = $conn->query("
    SELECT *
    FROM koordinator
    ORDER BY nama
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Koordinator - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Koordinator</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <a href="koordinator_add.php" class="btn btn-primary mb-20">Tambah Koordinator Baru</a>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($k = $koordinator->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $k['id']; ?></td>
                                    <td><?php echo htmlspecialchars($k['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($k['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($k['no_hp'] ?? '-'); ?></td>
                                    <td>
                                        <a href="koordinator_edit.php?id=<?php echo $k['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $k['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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

