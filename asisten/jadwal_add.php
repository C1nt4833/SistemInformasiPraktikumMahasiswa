<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $praktikum_id = intval($_POST['praktikum_id'] ?? 0);
    $hari = $_POST['hari'] ?? '';
    $waktu_mulai = $_POST['waktu_mulai'] ?? '';
    $waktu_selesai = $_POST['waktu_selesai'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    if ($praktikum_id <= 0) {
        $message = 'Pilih praktikum terlebih dahulu!';
        $messageType = 'error';
    } elseif (empty($hari)) {
        $message = 'Hari harus dipilih!';
        $messageType = 'error';
    } elseif (empty($waktu_mulai) || empty($waktu_selesai)) {
        $message = 'Waktu mulai dan selesai harus diisi!';
        $messageType = 'error';
    } elseif (empty($tanggal)) {
        $message = 'Tanggal harus diisi!';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO jadwal_praktikum (praktikum_id, hari, waktu_mulai, waktu_selesai, tanggal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $praktikum_id, $hari, $waktu_mulai, $waktu_selesai, $tanggal);
        
        if ($stmt->execute()) {
            $message = 'Jadwal praktikum berhasil ditambahkan!';
            $messageType = 'success';
            // Clear form
            $_POST = [];
        } else {
            $message = 'Gagal menambahkan jadwal praktikum!';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get all praktikum
$all_praktikum = $conn->query("SELECT * FROM praktikum ORDER BY nama_praktikum");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Praktikum - Asisten Dosen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Jadwal Praktikum</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="praktikum_id">Praktikum *</label>
                        <select id="praktikum_id" name="praktikum_id" required>
                            <option value="">-- Pilih Praktikum --</option>
                            <?php while ($p = $all_praktikum->fetch_assoc()): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($_POST['praktikum_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nama_praktikum']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="hari">Hari *</label>
                        <select id="hari" name="hari" required>
                            <option value="">-- Pilih Hari --</option>
                            <option value="Senin" <?php echo ($_POST['hari'] ?? '') == 'Senin' ? 'selected' : ''; ?>>Senin</option>
                            <option value="Selasa" <?php echo ($_POST['hari'] ?? '') == 'Selasa' ? 'selected' : ''; ?>>Selasa</option>
                            <option value="Rabu" <?php echo ($_POST['hari'] ?? '') == 'Rabu' ? 'selected' : ''; ?>>Rabu</option>
                            <option value="Kamis" <?php echo ($_POST['hari'] ?? '') == 'Kamis' ? 'selected' : ''; ?>>Kamis</option>
                            <option value="Jumat" <?php echo ($_POST['hari'] ?? '') == 'Jumat' ? 'selected' : ''; ?>>Jumat</option>
                            <option value="Sabtu" <?php echo ($_POST['hari'] ?? '') == 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>
                            <option value="Minggu" <?php echo ($_POST['hari'] ?? '') == 'Minggu' ? 'selected' : ''; ?>>Minggu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Tanggal *</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?php echo $_POST['tanggal'] ?? ''; ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="waktu_mulai">Waktu Mulai *</label>
                            <input type="time" id="waktu_mulai" name="waktu_mulai" value="<?php echo $_POST['waktu_mulai'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_selesai">Waktu Selesai *</label>
                            <input type="time" id="waktu_selesai" name="waktu_selesai" value="<?php echo $_POST['waktu_selesai'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
                        <a href="jadwal_praktikum.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
