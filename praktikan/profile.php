<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('praktikan');

$currentUser = getCurrentUser();

// Get praktikan data
$praktikan = $conn->query("
    SELECT p.*, u.nama, u.email 
    FROM praktikan p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.user_id = " . $currentUser['id']
)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Praktikan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Profile Praktikan</h2>
            </div>
            <div class="card-body">
                <?php if ($praktikan): ?>
                    <div style="max-width: 600px;">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" value="<?php echo htmlspecialchars($praktikan['nama']); ?>" readonly style="background-color: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" value="<?php echo htmlspecialchars($praktikan['nim']); ?>" readonly style="background-color: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($praktikan['email']); ?>" readonly style="background-color: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" value="<?php echo htmlspecialchars($praktikan['kelas'] ?? '-'); ?>" readonly style="background-color: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>Angkatan</label>
                            <input type="text" value="<?php echo htmlspecialchars($praktikan['angkatan'] ?? '-'); ?>" readonly style="background-color: #f5f5f5;">
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">Data praktikan tidak ditemukan!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

