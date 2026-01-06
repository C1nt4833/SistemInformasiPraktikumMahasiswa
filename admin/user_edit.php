<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: users.php');
    exit();
}

$message = '';
$messageType = '';

// Get user data
$user = $conn->query("SELECT * FROM users WHERE id = " . intval($id))->fetch_assoc();
if (!$user) {
    header('Location: users.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nim = $_POST['nim'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (!empty($nama) && !empty($email) && !empty($role)) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, password=?, nim=?, role=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama, $email, $hashedPassword, $nim, $role, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, nim=?, role=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama, $email, $nim, $role, $id);
        }
        
        if ($stmt->execute()) {
            $message = 'Pengguna berhasil diupdate!';
            $messageType = 'success';
            header('Location: users.php');
            exit();
        } else {
            $message = 'Gagal mengupdate pengguna!';
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Silakan isi semua field yang wajib!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Pengguna</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password">
                    </div>
                    
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" id="nim" name="nim" value="<?php echo htmlspecialchars($user['nim'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="asisten_dosen" <?php echo $user['role'] === 'asisten_dosen' ? 'selected' : ''; ?>>Asisten Dosen</option>
                            <option value="praktikan" <?php echo $user['role'] === 'praktikan' ? 'selected' : ''; ?>>Praktikan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="users.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

