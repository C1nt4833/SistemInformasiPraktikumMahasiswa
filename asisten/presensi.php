<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('asisten_dosen');

$currentUser = getCurrentUser();

// Get asisten data
$asisten_result = $conn->query("
    SELECT a.* FROM asisten_dosen a 
    WHERE a.user_id = " . $currentUser['id']
);
$asisten = $asisten_result ? $asisten_result->fetch_assoc() : null;

if (!$asisten) {
    die("Data asisten tidak ditemukan. Silakan hubungi administrator.");
}

$message = '';
$messageType = '';

// Handle update presensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_presensi'])) {
    $presensi_id = $_POST['presensi_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    
    if (!empty($presensi_id) && !empty($status)) {
        $asisten_id = $asisten['id'] ?? null;
        $stmt = $conn->prepare("UPDATE presensi SET status=?, keterangan=?, waktu_presensi=NOW(), asisten_dosen_id=? WHERE id=?");
        $stmt->bind_param("ssii", $status, $keterangan, $asisten_id, $presensi_id);
        
        if ($stmt->execute()) {
            $message = 'Presensi berhasil diupdate!';
            $messageType = 'success';
        } else {
            $message = 'Gagal mengupdate presensi!';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get praktikum filter
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
    <title>Kelola Presensi - Asisten Dosen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Kelola Presensi Praktikum</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
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
                
                <?php while ($j = $jadwal->fetch_assoc()): ?>
                    <div class="card mb-20">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($j['nama_praktikum']); ?> - <?php echo htmlspecialchars($j['nama_mk'] ?? ''); ?></h3>
                                <p><strong>Tanggal:</strong> <?php echo date('d/m/Y', strtotime($j['tanggal'])); ?> | 
                                   <strong>Hari:</strong> <?php echo $j['hari']; ?> | 
                                   <strong>Waktu:</strong> <?php echo date('H:i', strtotime($j['waktu_mulai'])); ?> - <?php echo date('H:i', strtotime($j['waktu_selesai'])); ?></p>
                                <div style="margin-top:10px;">
                                    <a href="presensi_add.php?jadwal_id=<?php echo $j['id']; ?>" class="btn btn-primary">Buat Presensi</a>
                                </div>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get presensi for this jadwal
                            $presensi = $conn->query("
                                SELECT pr.*, pk.nim, pk.kelas, u.nama 
                                FROM presensi pr
                                LEFT JOIN praktikan pk ON pr.praktikan_id = pk.id
                                LEFT JOIN users u ON pk.user_id = u.id
                                WHERE pr.jadwal_praktikum_id = " . $j['id'] . "
                                ORDER BY u.nama
                            ");
                            ?>
                            
                            <?php if ($presensi->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>NIM</th>
                                                <th>Kelas</th>
                                                <th>Status</th>
                                                <th>Waktu Presensi</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($pr = $presensi->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($pr['nama'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($pr['nim'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($pr['kelas'] ?? '-'); ?></td>
                                                    <td>
                                                        <span class="status-<?php echo str_replace('_', '-', $pr['status']); ?>">
                                                            <?php 
                                                            $status_text = [
                                                                'hadir' => 'Hadir',
                                                                'tidak_hadir' => 'Tidak Hadir',
                                                                'izin' => 'Izin',
                                                                'sakit' => 'Sakit'
                                                            ];
                                                            echo $status_text[$pr['status']] ?? $pr['status'];
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $pr['waktu_presensi'] ? date('d/m/Y H:i', strtotime($pr['waktu_presensi'])) : '-'; ?></td>
                                                    <td><?php echo htmlspecialchars($pr['keterangan'] ?? '-'); ?></td>
                                                    <td>
                                                         <button onclick="editPresensi(<?php echo htmlspecialchars(json_encode($pr)); ?>)" class="btn btn-info btn-sm">Edit</button>
                                                         
                                                         <form method="POST" action="presensi_edit.php" style="display:inline" onsubmit="return confirm('Hapus presensi ini?');">
                                                            <input type="hidden" name="delete_presensi" value="1">
                                                            <input type="hidden" name="presensi_id" value="<?php echo $pr['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                         </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>Belum ada presensi untuk jadwal ini</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- Edit Presensi Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
            <h3 style="margin-bottom: 20px;">Edit Presensi</h3>
            <form method="POST" action="">
                <input type="hidden" name="presensi_id" id="edit_presensi_id">
                <div class="form-group">
                    <label for="edit_status">Status *</label>
                    <select id="edit_status" name="status" required>
                        <option value="hadir">Hadir</option>
                        <option value="tidak_hadir">Tidak Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_keterangan">Keterangan</label>
                    <textarea id="edit_keterangan" name="keterangan"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="update_presensi" class="btn btn-primary">Update</button>
                    <button type="button" onclick="closeModal()" class="btn btn-danger">Batal</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function editPresensi(presensi) {
            document.getElementById('edit_presensi_id').value = presensi.id;
            document.getElementById('edit_status').value = presensi.status;
            document.getElementById('edit_keterangan').value = presensi.keterangan || '';
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>

