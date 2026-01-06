<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('praktikan');

$currentUser = getCurrentUser();

// Get praktikan data
$praktikan = $conn->query("
    SELECT * FROM praktikan 
    WHERE user_id = " . $currentUser['id']
)->fetch_assoc();

// Get praktikum for this praktikan
$praktikum_list = $conn->query("
    SELECT p.*, mk.nama_mk, mk.kode_mk
    FROM praktikum p
    LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
    LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
    WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
    ORDER BY p.semester, p.nama_praktikum
");

$view = $_GET['view'] ?? 'list'; // list, detail, schedule
$praktikum_id = $_GET['id'] ?? '';

// Get current date info
$today = date('Y-m-d');
$current_week_start = date('Y-m-d', strtotime('monday this week'));
$current_week_end = date('Y-m-d', strtotime('sunday this week'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktikum - Praktikan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Praktikum</h2>
            </div>
            <div class="card-body">
                <div class="action-buttons mb-20">
                    <a href="?view=list" class="btn <?php echo $view === 'list' ? 'btn-primary' : 'btn-info'; ?>">Daftar Praktikum</a>
                    <a href="?view=schedule&period=day" class="btn <?php echo $view === 'schedule' && ($_GET['period'] ?? '') === 'day' ? 'btn-primary' : 'btn-info'; ?>">Jadwal Hari Ini</a>
                    <a href="?view=schedule&period=week" class="btn <?php echo $view === 'schedule' && ($_GET['period'] ?? '') === 'week' ? 'btn-primary' : 'btn-info'; ?>">Jadwal Minggu Ini</a>
                </div>
                
                <?php if ($view === 'list'): ?>
                    <!-- List Praktikum -->
                    <?php if ($praktikum_list->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Praktikum</th>
                                        <th>Mata Kuliah</th>
                                        <th>Semester</th>
                                        <th>Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = $praktikum_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['nama_praktikum']); ?></td>
                                            <td><?php echo htmlspecialchars($p['kode_mk'] . ' - ' . $p['nama_mk']); ?></td>
                                            <td><?php echo $p['semester']; ?></td>
                                            <td><?php echo htmlspecialchars($p['lokasi'] ?? '-'); ?></td>
                                            <td>
                                                <a href="?view=detail&id=<?php echo $p['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📚</div>
                            <p>Belum ada praktikum yang terdaftar</p>
                        </div>
                    <?php endif; ?>
                    
                <?php elseif ($view === 'detail' && !empty($praktikum_id)): ?>
                    <!-- Detail Praktikum -->
                    <?php
                    $praktikum_detail = $conn->query("
                        SELECT p.*, mk.nama_mk, mk.kode_mk, k.nama as nama_koordinator, k.email as email_koordinator, k.no_hp as hp_koordinator, z.nama_pengawas, z.email as email_pengawas, z.no_hp as no_hp_pengawas
                        FROM praktikum p
                        LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                        LEFT JOIN koordinator k ON p.koordinator_id = k.id
                        LEFT JOIN pengawas_lab z ON p.id_pengawas = z.id_pengawas
                        WHERE p.id = " . intval($praktikum_id)
                    )->fetch_assoc();
                    
                    if ($praktikum_detail):
                        // Get jadwal
                        $jadwal = $conn->query("
                            SELECT * FROM jadwal_praktikum 
                            WHERE praktikum_id = " . intval($praktikum_id) . "
                            ORDER BY tanggal, waktu_mulai
                        ");
                        
                        // Get deadline laporan
                        $deadline = $conn->query("
                            SELECT * FROM deadline_laporan 
                            WHERE praktikum_id = " . intval($praktikum_id) . "
                            ORDER BY deadline
                        ");
                        
                        // Get asisten
                        $asisten = $conn->query("
                            SELECT a.*, k.nama as nama_koordinator
                            FROM asisten_dosen a
                            LEFT JOIN koordinator k ON a.koordinator_id = k.id
                            WHERE k.id = " . ($praktikum_detail['koordinator_id'] ?? 0) . "
                        ");
                        //get pengawas lab
                        $id_pengawas = $conn->query("
                            SELECT * FROM pengawas_lab
                            WHERE id_pengawas = " . ($praktikum_detail['id_pengawas'] ?? 0) . "
                        ");
                        
                        // Get presensi
                        $presensi = $conn->query("
                            SELECT pr.*, jp.tanggal, jp.hari, jp.waktu_mulai
                            FROM presensi pr
                            LEFT JOIN jadwal_praktikum jp ON pr.jadwal_praktikum_id = jp.id
                            WHERE pr.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                            AND jp.praktikum_id = " . intval($praktikum_id) . "
                            ORDER BY jp.tanggal DESC
                        ");
                    ?>
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($praktikum_detail['nama_praktikum']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Mata Kuliah:</strong> <?php echo htmlspecialchars($praktikum_detail['kode_mk'] . ' - ' . $praktikum_detail['nama_mk']); ?></p>
                                <p><strong>Semester:</strong> <?php echo $praktikum_detail['semester']; ?></p>
                                <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($praktikum_detail['lokasi'] ?? '-'); ?></p>
                                <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($praktikum_detail['deskripsi'] ?? '-'); ?></p>
                            </div>
                        </div>
                        
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Koordinator</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Nama:</strong> <?php echo htmlspecialchars($praktikum_detail['nama_koordinator'] ?? '-'); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($praktikum_detail['email_koordinator'] ?? '-'); ?></p>
                                <p><strong>No. HP:</strong> <?php echo htmlspecialchars($praktikum_detail['hp_koordinator'] ?? '-'); ?></p>
                            </div>
                        </div>

                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Pengawas Lab</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($praktikum_detail['id_pengawas'])): ?>
                                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($praktikum_detail['nama_pengawas'] ?? '-'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($praktikum_detail['email_pengawas'] ?? '-'); ?></p>
                                    <p><strong>No. HP:</strong> <?php echo htmlspecialchars($praktikum_detail['no_hp_pengawas'] ?? '-'); ?></p>
                                <?php else: ?>
                                    <p>Belum ada pengawas lab yang ditentukan</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Asisten Praktikum</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($asisten->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>No. HP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($a = $asisten->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($a['nama']); ?></td>
                                                        <td><?php echo htmlspecialchars($a['email'] ?? '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($a['no_hp'] ?? '-'); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Belum ada asisten yang ditugaskan</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Jadwal Praktikum</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($jadwal->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Hari</th>
                                                    <th>Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($j = $jadwal->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($j['tanggal'])); ?></td>
                                                        <td><?php echo $j['hari']; ?></td>
                                                        <td><?php echo date('H:i', strtotime($j['waktu_mulai'])); ?> - <?php echo date('H:i', strtotime($j['waktu_selesai'])); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Belum ada jadwal praktikum</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Deadline Laporan</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($deadline->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Judul Laporan</th>
                                                    <th>Deadline</th>
                                                    <th>Deskripsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($d = $deadline->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($d['judul_laporan']); ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($d['deadline'])); ?></td>
                                                        <td><?php echo htmlspecialchars($d['deskripsi'] ?? '-'); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Belum ada deadline laporan</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-20">
                            <div class="card-header">
                                <h3>Status Presensi</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($presensi->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Hari</th>
                                                    <th>Waktu</th>
                                                    <th>Status</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($pr = $presensi->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($pr['tanggal'])); ?></td>
                                                        <td><?php echo $pr['hari']; ?></td>
                                                        <td><?php echo date('H:i', strtotime($pr['waktu_mulai'])); ?></td>
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
                                                        <td><?php echo htmlspecialchars($pr['keterangan'] ?? '-'); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Belum ada data presensi</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <a href="?view=list" class="btn btn-primary">Kembali ke Daftar</a>
                    <?php else: ?>
                        <div class="alert alert-error">Praktikum tidak ditemukan!</div>
                        <a href="?view=list" class="btn btn-primary">Kembali ke Daftar</a>
                    <?php endif; ?>
                    
                <?php elseif ($view === 'schedule'): ?>
                    <!-- Schedule View -->
                    <?php
                    $period = $_GET['period'] ?? 'day';
                    
                    if ($period === 'day') {
                        $schedule_query = "
                            SELECT jp.*, p.nama_praktikum, mk.nama_mk, mk.kode_mk
                            FROM jadwal_praktikum jp
                            LEFT JOIN praktikum p ON jp.praktikum_id = p.id
                            LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                            LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
                            WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                            AND jp.tanggal = '$today'
                            ORDER BY jp.waktu_mulai
                        ";
                    } else {
                        $schedule_query = "
                            SELECT jp.*, p.nama_praktikum, mk.nama_mk, mk.kode_mk
                            FROM jadwal_praktikum jp
                            LEFT JOIN praktikum p ON jp.praktikum_id = p.id
                            LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                            LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
                            WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                            AND jp.tanggal BETWEEN '$current_week_start' AND '$current_week_end'
                            ORDER BY jp.tanggal, jp.waktu_mulai
                        ";
                    }
                    
                    $schedule = $conn->query($schedule_query);
                    ?>
                    
                    <h3><?php echo $period === 'day' ? 'Jadwal Praktikum Hari Ini (' . date('d/m/Y') . ')' : 'Jadwal Praktikum Minggu Ini (' . date('d/m/Y', strtotime($current_week_start)) . ' - ' . date('d/m/Y', strtotime($current_week_end)) . ')'; ?></h3>
                    
                    <?php if ($schedule->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Praktikum</th>
                                        <th>Mata Kuliah</th>
                                        <th>Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($s = $schedule->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($s['tanggal'])); ?></td>
                                            <td><?php echo $s['hari']; ?></td>
                                            <td><?php echo htmlspecialchars($s['nama_praktikum']); ?></td>
                                            <td><?php echo htmlspecialchars($s['kode_mk'] . ' - ' . $s['nama_mk']); ?></td>
                                            <td><?php echo date('H:i', strtotime($s['waktu_mulai'])); ?> - <?php echo date('H:i', strtotime($s['waktu_selesai'])); ?></td>
                                            <td>
                                                <a href="?view=detail&id=<?php echo $s['praktikum_id']; ?>" class="btn btn-info btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📅</div>
                            <p>Tidak ada jadwal praktikum untuk periode ini</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

