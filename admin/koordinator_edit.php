<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: koordinator.php');
    exit();
}

$message = '';
$messageType = '';

// Get koordinator data
$koordinator = $conn->query("SELECT * FROM koordinator WHERE id = " . intval($id))->fetch_assoc();
if (!$koordinator) {
    header('Location: koordinator.php');
    exit();
}

// Get users for dropdown
$users = $conn->query("
    SELECT * 
    FROM users 
    WHERE role IN ('asisten_dosen', 'admin')
    AND id NOT IN (
        SELECT user_id 
        FROM koordinator 
        WHERE user_id IS NOT NULL
    )
    ORDER BY nama
");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    
    if (!empty($nama)) {
        $stmt = $conn->prepare("UPDATE koordinator SET nama=?, email=?, no_hp=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $email, $no_hp, $id);
        
        if ($stmt->execute()) {
            $message = 'Koordinator berhasil diupdate!';
            $messageType = 'success';
            header('Location: koordinator.php');
            exit();
        } else {
            $message = 'Gagal mengupdate koordinator!';
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Silakan isi nama koordinator!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koordinator - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Koordinator</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
                  
                    
                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($koordinator['nama']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($koordinator['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($koordinator['no_hp'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="koordinator.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

