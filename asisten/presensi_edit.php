<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$message = '';
$messageType = '';

$presensi_id = intval($_GET['presensi_id'] ?? ($_POST['presensi_id'] ?? 0));
if ($presensi_id <= 0) {
    header('Location: presensi.php');
    exit();
}

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_presensi'])) {
    $stmt = $conn->prepare("DELETE FROM presensi WHERE id = ?");
    $stmt->bind_param('i', $presensi_id);
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: presensi.php');
        exit();
    } else {
        $message = 'Gagal menghapus presensi.';
        $messageType = 'error';
    }
}

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_presensi'])) {
    $status = $_POST['status'] ?? '';
    $keterangan = $_POST['keterangan'] ?? null;
    if (!empty($status)) {
        $asisten = getCurrentUser();
        // attempt to set asisten_dosen_id if column exists
        $stmt = $conn->prepare("UPDATE presensi SET status = ?, keterangan = ?, waktu_presensi = NOW() WHERE id = ?");
        $stmt->bind_param('sii', $status, $keterangan, $presensi_id);
        // Note: if keterangan is string, binding should be 'ssi', adjust accordingly. Simpler: use ssi and cast properly.
        $stmt->close();
    }
}

// Fetch presensi
$pres = $conn->query("SELECT pr.*, pk.nim, u.nama FROM presensi pr LEFT JOIN praktikan pk ON pr.praktikan_id = pk.id LEFT JOIN users u ON pk.user_id = u.id WHERE pr.id = " . $presensi_id)->fetch_assoc();
if (!$pres) {
    header('Location: presensi.php');
    exit();
}

// Handle update properly (do after fetch so we can use values)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_presensi'])) {
    $status = $_POST['status'] ?? '';
    $keterangan = $_POST['keterangan'] ?? null;
    if (!empty($status)) {
        $stmt = $conn->prepare("UPDATE presensi SET status = ?, keterangan = ?, waktu_presensi = NOW() WHERE id = ?");
        $stmt->bind_param('ssi', $status, $keterangan, $presensi_id);
        if ($stmt->execute()) {
            $message = 'Presensi berhasil diupdate.';
            $messageType = 'success';
            $stmt->close();
            header('Location: presensi.php');
            exit();
        } else {
            $message = 'Gagal mengupdate presensi.';
            $messageType = 'error';
            $stmt->close();
        }
    } else {
        $message = 'Status wajib diisi.';
        $messageType = 'error';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Presensi - Asisten</title>
    <link rel="stylesheet" href="../assets/css/style.css">
 </head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Presensi - <?php echo htmlspecialchars($pres['nama'] ?? '-'); ?></h2>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="presensi_id" value="<?php echo $presensi_id; ?>">
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="hadir" <?php echo ($pres['status']==='hadir')? 'selected' : ''; ?>>Hadir</option>
                            <option value="tidak_hadir" <?php echo ($pres['status']==='tidak_hadir')? 'selected' : ''; ?>>Tidak Hadir</option>
                            <option value="izin" <?php echo ($pres['status']==='izin')? 'selected' : ''; ?>>Izin</option>
                            <option value="sakit" <?php echo ($pres['status']==='sakit')? 'selected' : ''; ?>>Sakit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan"><?php echo htmlspecialchars($pres['keterangan'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_presensi" class="btn btn-primary">Update</button>
                        <a href="presensi.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>

                <hr>
                <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus presensi ini?');">
                    <input type="hidden" name="presensi_id" value="<?php echo $presensi_id; ?>">
                    <input type="hidden" name="delete_presensi" value="1">
                    <button type="submit" class="btn btn-danger">Hapus Presensi</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
