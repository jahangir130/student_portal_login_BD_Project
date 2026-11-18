<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT admin_id,name,email,password,role FROM admins WHERE email=? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];
        header('Location: admin_dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | Student Portal</title><link rel="stylesheet" href="assets/admin.css">
</head><body class="login-body">
<div class="login-card">
    <div class="brand-icon">SP</div>
    <p class="eyebrow">ADMINISTRATION PORTAL</p>
    <h1>Welcome back</h1>
    <p class="muted">Sign in to manage your student portal.</p>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Email address</label><input type="email" name="email" placeholder="admin@example.com" autocomplete="username" required>
        <label>Password</label><input type="password" name="password" placeholder="Enter password" autocomplete="current-password" required>
        <button class="btn primary full" type="submit">Sign in to Dashboard</button>
    </form>
    <?php if ($adminCount === 0): ?>
        <p class="login-foot">First time here? <a href="setup_admin.php">Create the first Admin account</a></p>
    <?php else: ?>
        <p class="login-foot">Need another administrator? Ask an existing Admin to add you from Admin Panel.</p>
    <?php endif; ?>
    <p class="login-foot"><a href="index.php">← Back to role selection</a> · <a href="student_login.php">Student login</a> · <a href="teacher_login.php">Teacher login</a></p>
</div></body></html>
