<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM notifikasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Notifikasi berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus notifikasi!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $isi = $_POST['isi'] ?? '';
    $tipe = $_POST['tipe'] ?? 'umum';
    $id = $_POST['id'] ?? '';
    
    if (!empty($judul) && !empty($isi)) {
        if (!empty($id)) {
            // Update
            $stmt = $conn->prepare("UPDATE notifikasi SET judul=?, isi=?, tipe=? WHERE id=?");
            $stmt->bind_param("sssi", $judul, $isi, $tipe, $id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO notifikasi (judul, isi, tipe) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $judul, $isi, $tipe);
        }
        
        if ($stmt->execute()) {
            $message = 'Notifikasi berhasil ' . (!empty($id) ? 'diupdate' : 'ditambahkan') . '!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menyimpan notifikasi!';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get all notifikasi
$notifikasi = $conn->query("SELECT * FROM notifikasi ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Notifikasi - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Notifikasi</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="" class="mb-20">
                    <input type="hidden" name="id" id="edit_id" value="">
                    <div class="form-group">
                        <label for="judul">Judul *</label>
                        <input type="text" id="judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="isi">Isi *</label>
                        <textarea id="isi" name="isi" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tipe">Tipe</label>
                        <select id="tipe" name="tipe">
                            <option value="umum">Umum</option>
                            <option value="jadwal">Jadwal</option>
                            <option value="penting">Penting</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-danger" onclick="document.getElementById('edit_id').value=''">Reset</button>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Isi</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($n = $notifikasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $n['id']; ?></td>
                                    <td><?php echo htmlspecialchars($n['judul']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($n['isi'], 0, 50)) . '...'; ?></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($n['tipe']); ?></span></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?></td>
                                    <td>
                                        <button onclick="editNotif(<?php echo htmlspecialchars(json_encode($n)); ?>)" class="btn btn-info btn-sm">Edit</button>
                                        <a href="?delete=<?php echo $n['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function editNotif(notif) {
            document.getElementById('edit_id').value = notif.id;
            document.getElementById('judul').value = notif.judul;
            document.getElementById('isi').value = notif.isi;
            document.getElementById('tipe').value = notif.tipe;
            document.getElementById('judul').focus();
        }
    </script>
</body>
</html>

