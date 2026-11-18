<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
if (!empty($_SESSION['student_id'])) { header('Location: student_dashboard.php'); exit; }

$error = ''; $success = '';
$old = ['id'=>'','name'=>'','dept'=>'','phn_num'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($old as $k=>$v) $old[$k] = trim($_POST[$k] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (in_array('', $old, true) || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!preg_match('/^[0-9A-Za-z_-]{3,30}$/', $old['id'])) {
        $error = 'Student ID must be 3–30 characters (letters, numbers, _ or -).';
    } elseif (strlen($old['name']) < 2 || strlen($old['name']) > 100) {
        $error = 'Please enter a valid full name.';
    } elseif (!preg_match('/^[0-9+()\-\s]{7,20}$/', $old['phn_num'])) {
        $error = 'Please enter a valid phone number.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirm password do not match.';
    } else {
        try {
            $pdo->beginTransaction();
            $st = $pdo->prepare('INSERT INTO student(id,name,dept) VALUES(?,?,?)');
            $st->execute([$old['id'],$old['name'],$old['dept']]);
            $st = $pdo->prepare('INSERT INTO `user`(id,phn_num,password) VALUES(?,?,?)');
            $st->execute([$old['id'],$old['phn_num'],password_hash($password,PASSWORD_DEFAULT)]);
            $pdo->commit();
            $success = 'Registration successful. You can now sign in.';
            $old = ['id'=>'','name'=>'','dept'=>'','phn_num'=>''];
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $ex->getCode() === '23000'
                ? 'This Student ID or phone number is already registered.'
                : 'Registration failed. Please check your database connection.';
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Registration | Student Portal</title><link rel="stylesheet" href="assets/auth.css">
</head><body>
<div class="auth-shell">
  <aside class="auth-aside">
    <div class="aside-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div>
    <div class="aside-copy"><span class="eyebrow">WELCOME TO YOUR CAMPUS</span><h1>Start your academic journey.</h1><p>Create your student account and access your academic information from one place.</p></div>
    <div class="aside-bottom">Simple access. Better campus experience.</div>
  </aside>
  <main class="auth-main">
    <section class="auth-card">
      <div class="mobile-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div>
      <span class="eyebrow">STUDENT ACCOUNT</span>
      <h2>Create your account</h2><p class="intro">Enter your details to register as a student.</p>
      <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
      <?php if($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
      <form method="POST" action="student_register.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-grid">
          <div class="field"><label>Student ID</label><input name="id" value="<?= e($old['id']) ?>" placeholder="Enter student ID" required></div>
          <div class="field"><label>Full name</label><input name="name" value="<?= e($old['name']) ?>" placeholder="Your full name" required></div>
          <div class="field"><label>Department</label><input name="dept" value="<?= e($old['dept']) ?>" placeholder="e.g. CSE" required></div>
          <div class="field"><label>Phone number</label><input name="phn_num" type="tel" value="<?= e($old['phn_num']) ?>" placeholder="01XXXXXXXXX" required></div>
          <div class="field"><label>Password</label><input name="password" type="password" minlength="8" placeholder="At least 8 characters" required></div>
          <div class="field"><label>Confirm password</label><input name="confirm_password" type="password" minlength="8" placeholder="Type password again" required></div>
        </div>
        <button class="submit-btn" type="submit">Create Account <span>→</span></button>
      </form>
      <p class="switch-text">Already have an account? <a href="student_login.php">Sign in here</a></p>
      <p class="admin-link">Administrator? <a href="admin_login.php">Admin login</a></p>
    </section>
  </main>
</div>
</body></html>
