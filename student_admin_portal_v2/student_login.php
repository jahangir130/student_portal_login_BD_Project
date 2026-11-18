<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
if (!empty($_SESSION['student_id'])) { header('Location: student_dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = trim($_POST['id'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($id === '' || $password === '') {
        $error = 'Enter your Student ID and password.';
    } else {
        $st = $pdo->prepare('SELECT s.id,s.name,s.dept,u.password FROM student s JOIN `user` u ON u.id=s.id WHERE s.id=?');
        $st->execute([$id]);
        $student = $st->fetch();
        if ($student && password_verify($password,$student['password'])) {
            session_regenerate_id(true);
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            header('Location: student_dashboard.php'); exit;
        }
        $error = 'Incorrect Student ID or password.';
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Login | Student Portal</title><link rel="stylesheet" href="assets/auth.css">
</head><body>
<div class="auth-shell">
  <aside class="auth-aside">
    <div class="aside-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div>
    <div class="aside-copy"><span class="eyebrow">YOUR CAMPUS SPACE</span><h1>Welcome back.</h1><p>Sign in to view your student profile and access your portal.</p></div>
    <div class="aside-bottom">Your academic information, all in one place.</div>
  </aside>
  <main class="auth-main">
    <section class="auth-card login-card">
      <div class="mobile-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div>
      <span class="eyebrow">STUDENT ACCOUNT</span>
      <h2>Sign in to your account</h2><p class="intro">Enter your registered Student ID and password.</p>
      <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
      <form method="POST" action="student_login.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="field"><label>Student ID</label><input name="id" placeholder="Enter your Student ID" autocomplete="username" required></div>
        <div class="field"><label>Password</label><input name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required></div>
        <button class="submit-btn" type="submit">Sign In <span>→</span></button>
      </form>
      <p class="switch-text">Don't have an account? <a href="student_register.php">Register here</a></p>
      <p class="admin-link"><a href="index.php">← Back to role selection</a> · <a href="teacher_login.php">Teacher login</a> · <a href="admin_login.php">Admin login</a></p>
    </section>
  </main>
</div>
</body></html>
