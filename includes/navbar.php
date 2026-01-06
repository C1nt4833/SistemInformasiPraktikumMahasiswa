<?php
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $currentUser['role'] ?? 'praktikan';

// Determine base path
$basePath = '';
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
    strpos($_SERVER['PHP_SELF'], '/asisten/') !== false || 
    strpos($_SERVER['PHP_SELF'], '/praktikan/') !== false) {
    $basePath = '../';
}

$dashboardPath = $basePath . ($role === 'admin' ? 'admin/dashboard.php' : ($role === 'asisten_dosen' ? 'asisten/dashboard.php' : 'praktikan/dashboard.php'));
$logoutPath = $basePath . 'logout.php';
?>
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?php echo $dashboardPath; ?>" class="navbar-brand">
            Sistem Informasi Praktikum
        </a>
        <ul class="navbar-menu">
            <li><span style="color: var(--white); padding: 8px 15px;"><?php echo htmlspecialchars($currentUser['nama']); ?> (<?php echo htmlspecialchars($currentUser['role']); ?>)</span></li>
            <li><a href="<?php echo $logoutPath; ?>">Logout</a></li>
        </ul>
    </div>
</nav>

