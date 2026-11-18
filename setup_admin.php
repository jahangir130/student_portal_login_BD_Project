<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

$count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$error = '';
$created = false;

if ($count > 0) {
    $error = 'An administrator already exists. Sign in through admin_login.php. To add another admin, use Admin Panel > Add Admin after login.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
        $error = 'Please enter a valid name, email and phone number.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirm password do not match.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO admins (name,email,phone,password,role) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT), 'Super Admin']);
            $created = true;
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000'
                ? 'This email or phone number is already registered.'
                : 'Could not create the first administrator. Check the admins table structure.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>First Admin Setup | Student Portal</title>
<link rel="stylesheet" href="assets/admin.css">
<link rel="stylesheet" href="assets/auth.css">
</head>
<body class="login-body">
<div class="login-card">
    <div class="brand-icon">SP</div>
    <p class="eyebrow">ONE-TIME ADMIN SETUP</p>
    <h1>Create First Admin</h1>
    <p class="muted">Create the first Super Admin account for this portal.</p>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($created): ?>
        <div class="success">Super Admin created successfully. Now sign in with your email and password.</div>
        <a class="btn primary full" href="admin_login.php">Go to Admin Login</a>
        <p class="login-foot">For security, delete setup_admin.php after confirming login works.</p>
    <?php elseif ($count === 0): ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Full name</label><input name="name" required maxlength="100" placeholder="Your name">
        <label>Email address</label><input type="email" name="email" required maxlength="150" placeholder="admin@example.com">
        <label>Phone number</label><input name="phone" required maxlength="20" placeholder="01XXXXXXXXX">
        <label>Password (minimum 8 characters)</label><input type="password" name="password" minlength="8" required>
        <label>Confirm password</label><input type="password" name="confirm_password" minlength="8" required>
        <button class="btn primary full" type="submit">Create Super Admin</button>
    </form>
    <?php endif; ?>
    <p class="login-foot"><a href="admin_login.php">Back to Admin Login</a></p>
</div>
</body></html>
