<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nim = $_POST['nim'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (!empty($nama) && !empty($email) && !empty($password) && !empty($role)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, nim, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $hashedPassword, $nim, $role);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            
            // If praktikan, also add to praktikan table
            if ($role === 'praktikan' && !empty($nim)) {
                $kelas = $_POST['kelas'] ?? '';
                $angkatan = $_POST['angkatan'] ?? date('Y');
                $stmt2 = $conn->prepare("INSERT INTO praktikan (user_id, nim, kelas, angkatan) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("isss", $userId, $nim, $kelas, $angkatan);
                $stmt2->execute();
                $stmt2->close();
            }
            
            $message = 'Pengguna berhasil ditambahkan!';
            $messageType = 'success';
            header('Location: users.php');
            exit();
        } else {
            $message = 'Gagal menambahkan pengguna!';
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
    <title>Tambah Pengguna - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Pengguna Baru</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" id="nim" name="nim">
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required onchange="togglePraktikanFields()">
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="asisten_dosen">Asisten Dosen</option>
                            <option value="praktikan">Praktikan</option>
                        </select>
                    </div>
                    
                    <div id="praktikanFields" style="display: none;">
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <input type="text" id="kelas" name="kelas">
                        </div>
                        
                        <div class="form-group">
                            <label for="angkatan">Angkatan</label>
                            <input type="number" id="angkatan" name="angkatan" min="2000" max="2099" value="<?php echo date('Y'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="users.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function togglePraktikanFields() {
            const role = document.getElementById('role').value;
            const praktikanFields = document.getElementById('praktikanFields');
            if (role === 'praktikan') {
                praktikanFields.style.display = 'block';
            } else {
                praktikanFields.style.display = 'none';
            }
        }
    </script>
</body>
</html>

