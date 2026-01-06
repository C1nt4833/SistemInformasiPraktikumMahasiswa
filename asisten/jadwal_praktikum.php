<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$currentUser = getCurrentUser();
$message = '';
$messageType = '';

// Get asisten data
$asisten_result = $conn->query("
    SELECT a.* FROM asisten_dosen a 
    WHERE a.user_id = " . $currentUser['id']
);
$asisten = $asisten_result ? $asisten_result->fetch_assoc() : null;

if (!$asisten) {
    die("Data asisten tidak ditemukan. Silakan hubungi administrator.");
}

// Handle delete jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_jadwal'])) {
    $jadwal_id = intval($_POST['jadwal_id'] ?? 0);
    if ($jadwal_id > 0) {
        $stmt = $conn->prepare("DELETE FROM jadwal_praktikum WHERE id = ?");
        $stmt->bind_param("i", $jadwal_id);
        if ($stmt->execute()) {
            $message = 'Jadwal berhasil dihapus!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menghapus jadwal!';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get filter praktikum
$praktikum_id = $_GET['praktikum_id'] ?? '';

// Get jadwal praktikum
$jadwal_query = "
    SELECT jp.*, p.nama_praktikum, mk.nama_mk 
    FROM jadwal_praktikum jp
    LEFT JOIN praktikum p ON jp.praktikum_id = p.id
    LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
    WHERE 1=1
";

if (!empty($praktikum_id)) {
    $jadwal_query .= " AND jp.praktikum_id = " . intval($praktikum_id);
}

$jadwal_query .= " ORDER BY jp.tanggal DESC, jp.waktu_mulai";

$jadwal = $conn->query($jadwal_query);

// Get all praktikum for filter
$all_praktikum = $conn->query("SELECT * FROM praktikum ORDER BY nama_praktikum");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Jadwal Praktikum - Asisten Dosen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Atur Jadwal Praktikum</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <div class="mb-20">
                    <a href="jadwal_add.php" class="btn btn-primary">+ Tambah Jadwal</a>
                </div>
                
                <form method="GET" action="" class="mb-20">
                    <div class="form-group" style="max-width: 300px;">
                        <label for="praktikum_id">Filter Praktikum</label>
                        <select id="praktikum_id" name="praktikum_id" onchange="this.form.submit()">
                            <option value="">Semua Praktikum</option>
                            <?php while ($p = $all_praktikum->fetch_assoc()): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($praktikum_id == $p['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nama_praktikum']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
                
                <?php if ($jadwal->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Praktikum</th>
                                    <th>Mata Kuliah</th>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($j = $jadwal->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($j['nama_praktikum'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($j['nama_mk'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($j['hari'] ?? '-'); ?></td>
                                        <td><?php echo $j['tanggal'] ? date('d/m/Y', strtotime($j['tanggal'])) : '-'; ?></td>
                                        <td>
                                            <?php 
                                            if ($j['waktu_mulai'] && $j['waktu_selesai']) {
                                                echo date('H:i', strtotime($j['waktu_mulai'])) . ' - ' . date('H:i', strtotime($j['waktu_selesai']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="jadwal_edit.php?jadwal_id=<?php echo $j['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                            <form method="POST" action="" style="display:inline" onsubmit="return confirm('Hapus jadwal ini? Presensi yang terkait juga akan dihapus.');">
                                                <input type="hidden" name="delete_jadwal" value="1">
                                                <input type="hidden" name="jadwal_id" value="<?php echo $j['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Tidak ada jadwal praktikum. <a href="jadwal_add.php">Buat jadwal baru</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
