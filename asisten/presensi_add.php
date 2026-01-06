<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$message = '';
$messageType = '';

$jadwal_id = intval($_GET['jadwal_id'] ?? 0);
if ($jadwal_id <= 0) {
    header('Location: presensi.php');
    exit();
}

// get jadwal and praktikum id
$row = $conn->query("SELECT jp.*, p.id AS praktikum_id, p.nama_praktikum FROM jadwal_praktikum jp LEFT JOIN praktikum p ON jp.praktikum_id = p.id WHERE jp.id = " . $jadwal_id)->fetch_assoc();
if (!$row) {
    header('Location: presensi.php');
    exit();
}

$praktikum_id = $row['praktikum_id'];

// get praktikan for this praktikum
$praktikan_rs = $conn->query("
    SELECT pk.id, pk.nim, u.nama 
    FROM praktikan pk 
    LEFT JOIN users u ON pk.user_id = u.id 
    LEFT JOIN praktikan_praktikum pp ON pk.id = pp.praktikan_id 
    WHERE pp.praktikum_id = " . intval($praktikum_id) . " 
    ORDER BY u.nama
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_presensi'])) {
    $selected = $_POST['praktikan_id'] ?? [];
    if (!is_array($selected) || count($selected) === 0) {
        $message = 'Pilih minimal satu praktikan.';
        $messageType = 'error';
    } else {
        $stmt_ins = $conn->prepare("INSERT INTO presensi (jadwal_praktikum_id, praktikan_id, status, waktu_presensi) VALUES (?, ?, ?, NOW())");
        $stmt_check = $conn->prepare("SELECT id FROM presensi WHERE jadwal_praktikum_id = ? AND praktikan_id = ? LIMIT 1");
        $created = 0;
        foreach ($selected as $pid) {
            $pid = intval($pid);
            if ($pid <= 0) continue;
            // skip if exists
            $stmt_check->bind_param('ii', $jadwal_id, $pid);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) continue;
            $default_status = 'tidak_hadir';
            $stmt_ins->bind_param('iis', $jadwal_id, $pid, $default_status);
            if ($stmt_ins->execute()) $created++;
        }
        $stmt_ins->close();
        $stmt_check->close();
        if ($created > 0) {
            $message = "Berhasil membuat $created presensi.";
            $messageType = 'success';
            header('Location: presensi.php?praktikum_id=' . intval($praktikum_id));
            exit();
        } else {
            $message = 'Tidak ada presensi baru yang dibuat (mungkin sudah ada).';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Buat Presensi - Asisten</title>
    <link rel="stylesheet" href="../assets/css/style.css">
 </head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Buat Presensi untuk: <?php echo htmlspecialchars($row['nama_praktikum']); ?></h2>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="create_presensi" value="1">
                    <input type="hidden" name="jadwal_id" value="<?php echo $jadwal_id; ?>">
                    <p>Pilih praktikan yang akan dibuatkan presensinya (default status: tidak_hadir):</p>
                    <div style="max-height: 350px; overflow:auto; border:1px solid #eee; padding:8px;">
                        <?php while ($pk = $praktikan_rs->fetch_assoc()): ?>
                            <label style="display:block; margin:6px 0;">
                                <input type="checkbox" name="praktikan_id[]" value="<?php echo $pk['id']; ?>"> <?php echo htmlspecialchars($pk['nama'] . ' - ' . $pk['nim']); ?>
                            </label>
                        <?php endwhile; ?>
                    </div>
                    <div class="form-group" style="margin-top:12px;">
                        <button class="btn btn-primary" type="submit">Buat Presensi</button>
                        <a href="presensi.php?praktikum_id=<?php echo $praktikum_id; ?>" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
