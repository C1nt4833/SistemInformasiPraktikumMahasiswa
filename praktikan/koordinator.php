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
    SELECT p.*, mk.nama_mk, mk.kode_mk, k.id as koordinator_id, k.nama as nama_koordinator
    FROM praktikum p
    LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
    LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
    LEFT JOIN koordinator k ON p.koordinator_id = k.id
    WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
    ORDER BY p.nama_praktikum
");

$view = $_GET['view'] ?? 'list'; // list, koordinator, asisten, data_asisten
$koordinator_id = $_GET['koordinator_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koordinator - Praktikan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Koordinator</h2>
            </div>
            <div class="card-body">
                <div class="action-buttons mb-20">
                    <a href="?view=list" class="btn <?php echo $view === 'list' ? 'btn-primary' : 'btn-info'; ?>">Data Praktikum</a>
                    <a href="?view=mata_kuliah" class="btn <?php echo $view === 'mata_kuliah' ? 'btn-primary' : 'btn-info'; ?>">Informasi Mata Kuliah</a>
                    <a href="?view=koordinator" class="btn <?php echo $view === 'koordinator' ? 'btn-primary' : 'btn-info'; ?>">Informasi Koordinator</a>
                    <a href="?view=data_asisten" class="btn <?php echo $view === 'data_asisten' ? 'btn-primary' : 'btn-info'; ?>">Data Asisten</a>
                </div>
                
                <?php if ($view === 'list'): ?>
                    <!-- Data Praktikum -->
                    <h3>Data Praktikum</h3>
                    <?php if ($praktikum_list->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Praktikum</th>
                                        <th>Mata Kuliah</th>
                                        <th>Semester</th>
                                        <th>Koordinator</th>
                                        <th>Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = $praktikum_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['nama_praktikum']); ?></td>
                                            <td><?php echo htmlspecialchars($p['kode_mk'] . ' - ' . $p['nama_mk']); ?></td>
                                            <td><?php echo $p['semester']; ?></td>
                                            <td><?php echo htmlspecialchars($p['nama_koordinator'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($p['lokasi'] ?? '-'); ?></td>
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
                    
                <?php elseif ($view === 'mata_kuliah'): ?>
                    <!-- Informasi Mata Kuliah -->
                    <h3>Informasi Mata Kuliah Praktikum</h3>
                    <?php
                    $mata_kuliah = $conn->query("
                        SELECT DISTINCT mk.*, COUNT(p.id) as jumlah_praktikum
                        FROM mata_kuliah mk
                        LEFT JOIN praktikum p ON mk.id = p.mata_kuliah_id
                        LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
                        WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                        GROUP BY mk.id
                        ORDER BY mk.semester, mk.nama_mk
                    ");
                    ?>
                    
                    <?php if ($mata_kuliah->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kode MK</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th>Semester</th>
                                        <th>SKS</th>
                                        <th>Jumlah Praktikum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($mk = $mata_kuliah->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($mk['kode_mk']); ?></td>
                                            <td><?php echo htmlspecialchars($mk['nama_mk']); ?></td>
                                            <td><?php echo $mk['semester']; ?></td>
                                            <td><?php echo $mk['sks']; ?></td>
                                            <td><?php echo $mk['jumlah_praktikum']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📖</div>
                            <p>Belum ada mata kuliah praktikum</p>
                        </div>
                    <?php endif; ?>
                    
                <?php elseif ($view === 'koordinator'): ?>
                    <!-- Informasi Koordinator -->
                    <h3>Informasi Koordinator Praktikum</h3>
                    <?php
                    $koordinator_list = $conn->query("
                        SELECT DISTINCT k.*, COUNT(DISTINCT p.id) as jumlah_praktikum, COUNT(DISTINCT a.id) as jumlah_asisten
                        FROM koordinator k
                        LEFT JOIN praktikum p ON k.id = p.koordinator_id
                        LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
                        LEFT JOIN asisten_dosen a ON k.id = a.koordinator_id
                        WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                        GROUP BY k.id
                        ORDER BY k.nama
                    ");
                    ?>
                    
                    <?php if ($koordinator_list->num_rows > 0): ?>
                        <?php while ($k = $koordinator_list->fetch_assoc()): ?>
                            <div class="card mb-20">
                                <div class="card-header">
                                    <h3><?php echo htmlspecialchars($k['nama']); ?></h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($k['email'] ?? '-'); ?></p>
                                    <p><strong>No. HP:</strong> <?php echo htmlspecialchars($k['no_hp'] ?? '-'); ?></p>
                                    <p><strong>Jumlah Praktikum:</strong> <?php echo $k['jumlah_praktikum']; ?></p>
                                    <p><strong>Jumlah Asisten:</strong> <?php echo $k['jumlah_asisten']; ?></p>
                                    
                                    <h4 style="margin-top: 20px;">Asisten Praktikum</h4>
                                    <?php
                                    $asisten = $conn->query("
                                        SELECT * FROM asisten_dosen 
                                        WHERE koordinator_id = " . $k['id'] . "
                                        ORDER BY nama
                                    ");
                                    ?>
                                    
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
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">👨‍🏫</div>
                            <p>Belum ada koordinator praktikum</p>
                        </div>
                    <?php endif; ?>
                    
                <?php elseif ($view === 'data_asisten'): ?>
                    <!-- Data Asisten -->
                    <h3>Data Asisten Praktikum</h3>
                    <?php
                    $asisten_list = $conn->query("
                        SELECT DISTINCT a.*, k.nama as nama_koordinator
                        FROM asisten_dosen a
                        LEFT JOIN koordinator k ON a.koordinator_id = k.id
                        LEFT JOIN praktikum p ON k.id = p.koordinator_id
                        LEFT JOIN praktikan_praktikum pp ON p.id = pp.praktikum_id
                        WHERE pp.praktikan_id = " . ($praktikan['id'] ?? 0) . "
                        ORDER BY a.nama
                    ");
                    ?>
                    
                    <?php if ($asisten_list->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. HP</th>
                                        <th>Koordinator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($a = $asisten_list->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($a['email'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($a['no_hp'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($a['nama_koordinator'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">👨‍💼</div>
                            <p>Belum ada data asisten</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

