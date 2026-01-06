<?php
require_once '../config/database.php';
require_once '../config/session.php';
checkRole('admin');

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: asisten.php');
    exit();
}

$message = '';
$messageType = '';

// Get asisten data
$asisten = $conn->query("SELECT * FROM asisten_dosen WHERE id = " . intval($id))->fetch_assoc();
if (!$asisten) {
    header('Location: asisten.php');
    exit();
}

// Get users and koordinator for dropdowns
$users = $conn->query("SELECT * FROM users WHERE role IN ('asisten_dosen', 'admin') OR id NOT IN (SELECT user_id FROM asisten_dosen WHERE user_id IS NOT NULL AND id != " . intval($id) . ") ORDER BY nama");
$koordinator = $conn->query("SELECT * FROM koordinator ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $koordinator_id = $_POST['koordinator_id'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $user_id = $_POST['user_id'] ?? null;
    
    if ($user_id === '') {
        $user_id = null;
    }

    $koordinator_id = $_POST['koordinator_id'] ?? null;
    if ($koordinator_id === '') {
        $koordinator_id = null;
    }

    if (!empty($nama)) {
        $stmt = $conn->prepare("UPDATE asisten_dosen SET user_id=?, koordinator_id=?, nama=?, email=?, no_hp=? WHERE id=?");
        $stmt->bind_param("iisssi", $user_id, $koordinator_id, $nama, $email, $no_hp, $id);
        
        if ($stmt->execute()) {
            $message = 'Asisten berhasil diupdate!';
            $messageType = 'success';
            header('Location: asisten.php');
            exit();
        } else {
            $message = 'Gagal mengupdate asisten!';
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Silakan isi nama asisten!';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asisten - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Edit Asisten</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="card-body">
                <form method="POST" action="">
            
                    
                    <div class="form-group">
                        <label for="koordinator_id">Koordinator</label>
                        <select id="koordinator_id" name="koordinator_id">
                            <option value="">Tidak ada koordinator</option>
                            <?php while ($k = $koordinator->fetch_assoc()): ?>
                                <option value="<?php echo $k['id']; ?>" <?php echo $k['id'] == $asisten['koordinator_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($k['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($asisten['nama']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($asisten['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($asisten['no_hp'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="asisten.php" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

