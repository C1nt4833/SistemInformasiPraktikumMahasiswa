<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');
$id = $_GET['id_pengawas'] ?? '';
$message = '';
$messageType = '';

// Get users for dropdown
$users = $conn->query("
    SELECT * 
    FROM users 
    WHERE role IN ('asisten_dosen', 'admin')
    AND id NOT IN (
        SELECT user_id 
        FROM pengawas_lab 
        WHERE user_id IS NOT NULL
    )
    ORDER BY nama
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = $_POST['nama_pengawas'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    
    if (!empty($nama)) {
        $stmt = $conn->prepare("INSERT INTO pengawas_lab (nama_pengawas, email, no_hp) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $email, $no_hp);
        
        if ($stmt->execute()) {
            $message = 'Pengawas berhasil ditambahkan!';
            $messageType = 'success';
            header('Location: pengawas.php');
            exit();
        } else {
            $message = 'Gagal menambahkan pengawas!';
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Silakan isi nama pengawas!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengawas - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Pengawas Baru</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
            <form method="POST" action="">

                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" id="nama" name="nama_pengawas" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="pengawas.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

