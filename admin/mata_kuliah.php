<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM mata_kuliah WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Mata kuliah berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus mata kuliah!';
        $messageType = 'error';
    }
    $stmt->close();
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_mk = $_POST['kode_mk'] ?? '';
    $nama_mk = $_POST['nama_mk'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $sks = $_POST['sks'] ?? '';
    $id = $_POST['id'] ?? '';
    
    if (!empty($kode_mk) && !empty($nama_mk) && !empty($semester) && !empty($sks)) {
        if (!empty($id)) {
            // Update
            $stmt = $conn->prepare("UPDATE mata_kuliah SET kode_mk=?, nama_mk=?, semester=?, sks=? WHERE id=?");
            $stmt->bind_param("ssiii", $kode_mk, $nama_mk, $semester, $sks, $id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, semester, sks) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $kode_mk, $nama_mk, $semester, $sks);
        }
        
        if ($stmt->execute()) {
            $message = 'Mata kuliah berhasil ' . (!empty($id) ? 'diupdate' : 'ditambahkan') . '!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menyimpan mata kuliah!';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get all mata kuliah
$mata_kuliah = $conn->query("SELECT * FROM mata_kuliah ORDER BY semester, nama_mk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mata Kuliah - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Mata Kuliah</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="" class="mb-20">
                    <input type="hidden" name="id" id="edit_id" value="">
                    <div style="display: grid; grid-template-columns: 2fr 3fr 1fr 1fr auto; gap: 10px; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="kode_mk">Kode MK *</label>
                            <input type="text" id="kode_mk" name="kode_mk" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="nama_mk">Nama Mata Kuliah *</label>
                            <input type="text" id="nama_mk" name="nama_mk" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="semester">Semester *</label>
                            <input type="number" id="semester" name="semester" min="1" max="14" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="sks">SKS *</label>
                            <input type="number" id="sks" name="sks" min="1" max="6" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Semester</th>
                                <th>SKS</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($mk = $mata_kuliah->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $mk['id']; ?></td>
                                    <td><?php echo htmlspecialchars($mk['kode_mk']); ?></td>
                                    <td><?php echo htmlspecialchars($mk['nama_mk']); ?></td>
                                    <td><?php echo $mk['semester']; ?></td>
                                    <td><?php echo $mk['sks']; ?></td>
                                    <td>
                                        <button onclick="editMK(<?php echo htmlspecialchars(json_encode($mk)); ?>)" class="btn btn-info btn-sm">Edit</button>
                                        <a href="?delete=<?php echo $mk['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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
        function editMK(mk) {
            document.getElementById('edit_id').value = mk.id;
            document.getElementById('kode_mk').value = mk.kode_mk;
            document.getElementById('nama_mk').value = mk.nama_mk;
            document.getElementById('semester').value = mk.semester;
            document.getElementById('sks').value = mk.sks;
            document.getElementById('kode_mk').focus();
        }
    </script>
</body>
</html>

