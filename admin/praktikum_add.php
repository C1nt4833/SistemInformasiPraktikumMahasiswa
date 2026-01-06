<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

// Get mata kuliah, koordinator, and pengawas for dropdowns
$mata_kuliah = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_mk");
$koordinator = $conn->query("SELECT * FROM koordinator ORDER BY nama");
$pengawas = $conn->query("SELECT id_pengawas, nama_pengawas FROM pengawas_lab ORDER BY nama_pengawas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah_id = $_POST['mata_kuliah_id'] ?? '';
    $koordinator_id = $_POST['koordinator_id'] ?? '';
    $id_pengawas = $_POST['id_pengawas'] ?? null;
    $nama_praktikum = $_POST['nama_praktikum'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    
    if (!empty($mata_kuliah_id) && !empty($koordinator_id) && !empty($nama_praktikum) && !empty($semester)) {
        $stmt = $conn->prepare("INSERT INTO praktikum (mata_kuliah_id, koordinator_id, id_pengawas, nama_praktikum, semester, lokasi, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiisss", $mata_kuliah_id, $koordinator_id, $id_pengawas, $nama_praktikum, $semester, $lokasi, $deskripsi);
        
        if ($stmt->execute()) {
            $message = 'Praktikum berhasil ditambahkan!';
            $messageType = 'success';
            header('Location: praktikum.php');
            exit();
        } else {
            $message = 'Gagal menambahkan praktikum!';
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Silakan isi semua field yang wajib!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Praktikum - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Praktikum Baru</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="mata_kuliah_id">Mata Kuliah *</label>
                        <select id="mata_kuliah_id" name="mata_kuliah_id" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php while ($mk = $mata_kuliah->fetch_assoc()): ?>
                                <option value="<?php echo $mk['id']; ?>"><?php echo htmlspecialchars($mk['kode_mk'] . ' - ' . $mk['nama_mk']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="koordinator_id">Koordinator *</label>
                        <select id="koordinator_id" name="koordinator_id" required>
                            <option value="">Pilih Koordinator</option>
                            <?php while ($k = $koordinator->fetch_assoc()): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_pengawas">Pengawas Lab</label>
                        <select id="id_pengawas" name="id_pengawas">
                            <option value="">Pilih Pengawas Lab</option>
                            <?php while ($p = $pengawas->fetch_assoc()): ?>
                                <option value="<?php echo $p['id_pengawas']; ?>"><?php echo htmlspecialchars($p['nama_pengawas']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_praktikum">Nama Praktikum *</label>
                        <input type="text" id="nama_praktikum" name="nama_praktikum" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">Semester *</label>
                        <input type="number" id="semester" name="semester" min="1" max="14" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="praktikum.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

