<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM pengawas_lab WHERE id_pengawas = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Pengawas berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus koordinator!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Get all pengawas
$pengawas_lab = $conn->query("
    SELECT * 
    FROM pengawas_lab
    ORDER BY nama_pengawas

");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengawas Laboratorium - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Pengawas Laboratorium</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <a href="pengawas_add.php" class="btn btn-primary mb-20">Tambah Pengawas Baru</a>
                
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
                            <?php while ($z = $pengawas_lab->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $z['id_pengawas']; ?></td>
                                    <td><?php echo htmlspecialchars($z['nama_pengawas']); ?></td>
                                    <td><?php echo htmlspecialchars($z['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($z['no_hp'] ?? '-'); ?></td>
                                    <td>
                                        <a href="pengawas_edit.php?id_pengawas=<?php echo $z['id_pengawas']; ?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $z['id_pengawas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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

