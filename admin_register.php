<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Registration of additional administrators is restricted to an authenticated admin.
if (!empty($_SESSION['admin_id'])) {
    header('Location: admin_form.php');
    exit;
}
$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Registration | Student Portal</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="login-body">
    <div class="login-card">
        <div class="brand-icon">SP</div>
        <p class="eyebrow">ADMINISTRATION PORTAL</p>
        <h1>Admin registration</h1>
        <?php if ($adminCount === 0): ?>
        <p class="muted">No administrator account exists yet. Create the first Super Admin account to set up the portal.
        </p>
        <a class="btn primary full" href="setup_admin.php">Create first Admin account</a>
        <?php else: ?>
        <p class="muted">For security, new administrator accounts can only be created by an existing Admin.</p>
        <p class="login-foot">1. Sign in with your Admin account.</p>
        <p class="login-foot">2. Open Admin Panel and choose Add Admin.</p>
        <a class="btn primary full" href="admin_login.php">Sign in to continue</a>
        <?php endif; ?>
        <p class="login-foot"><a href="admin_login.php">← Back to Admin Login</a></p>
    </div>
</body>

</html>