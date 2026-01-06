<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$id = intval($_GET['id'] ?? 0);
if (empty($id)) {
    header('Location: praktikum.php');
    exit();
}

$message = '';
$messageType = '';

// Get praktikum data
$praktikum = $conn->query("SELECT * FROM praktikum WHERE id = " . intval($id))->fetch_assoc();
if (!$praktikum) {
    header('Location: praktikum.php');
    exit();
}

// Get mata kuliah, koordinator, and pengawas for dropdowns
$mata_kuliah = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_mk");
$koordinator = $conn->query("SELECT * FROM koordinator ORDER BY nama");
$pengawas = $conn->query("SELECT id_pengawas, nama_pengawas FROM pengawas_lab ORDER BY nama_pengawas");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah_id = intval($_POST['mata_kuliah_id'] ?? 0);
    $koordinator_id = intval($_POST['koordinator_id'] ?? 0);
    $id_pengawas = !empty($_POST['id_pengawas']) ? intval($_POST['id_pengawas']) : null;
    $nama_praktikum = $_POST['nama_praktikum'] ?? '';
    $semester = intval($_POST['semester'] ?? 0);
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    
    if (!empty($mata_kuliah_id) && !empty($koordinator_id) && !empty($nama_praktikum) && !empty($semester)) {
        $stmt = $conn->prepare("UPDATE praktikum SET mata_kuliah_id=?, koordinator_id=?, id_pengawas=?, nama_praktikum=?, semester=?, lokasi=?, deskripsi=? WHERE id=?");
        $stmt->bind_param("iiisissi", $mata_kuliah_id, $koordinator_id, $id_pengawas, $nama_praktikum, $semester, $lokasi, $deskripsi, $id);
        
        if ($stmt->execute()) {
            $message = 'Praktikum berhasil diupdate!';
            $messageType = 'success';
            header('Location: praktikum.php');
            exit();
        } else {
            $message = 'Gagal mengupdate praktikum!';
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
    <title>Edit Praktikum - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Praktikum</h2>
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
                                <option value="<?php echo $mk['id']; ?>" <?php echo $mk['id'] == $praktikum['mata_kuliah_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mk['kode_mk'] . ' - ' . $mk['nama_mk']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="koordinator_id">Koordinator *</label>
                        <select id="koordinator_id" name="koordinator_id" required>
                            <option value="">Pilih Koordinator</option>
                            <?php while ($k = $koordinator->fetch_assoc()): ?>
                                <option value="<?php echo $k['id']; ?>" <?php echo $k['id'] == $praktikum['koordinator_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($k['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_pengawas">Pengawas Lab</label>
                        <select id="id_pengawas" name="id_pengawas">
                            <option value="">Pilih Pengawas Lab</option>
                            <?php while ($p = $pengawas->fetch_assoc()): ?>
                                <option value="<?php echo $p['id_pengawas']; ?>" <?php echo $p['id_pengawas'] == $praktikum['id_pengawas'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nama_pengawas']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_praktikum">Nama Praktikum *</label>
                        <input type="text" id="nama_praktikum" name="nama_praktikum" value="<?php echo htmlspecialchars($praktikum['nama_praktikum']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">Semester *</label>
                        <input type="number" id="semester" name="semester" min="1" max="14" value="<?php echo $praktikum['semester']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars($praktikum['lokasi'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi"><?php echo htmlspecialchars($praktikum['deskripsi'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="praktikum.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

