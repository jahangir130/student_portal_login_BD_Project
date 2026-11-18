<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(!empty($_SESSION['teacher_id'])){header('Location: teacher_dashboard.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$email=trim($_POST['email']??'');$password=$_POST['password']??'';
 $st=$pdo->prepare('SELECT teacher_id,name,email,department,designation,password,account_status FROM teachers WHERE email=? LIMIT 1');
 $st->execute([$email]);$teacher=$st->fetch();
 if($teacher && ($teacher['account_status'] ?? 'approved') === 'pending' && !empty($teacher['password']) && password_verify($password,$teacher['password'])){ $error='Your registration is waiting for administrator approval.'; }
 elseif($teacher&&!empty($teacher['password'])&&($teacher['account_status'] ?? 'approved')==='approved'&&password_verify($password,$teacher['password'])){
  session_regenerate_id(true);$_SESSION['teacher_id']=$teacher['teacher_id'];$_SESSION['teacher_name']=$teacher['name'];
  header('Location: teacher_dashboard.php');exit;
 }
 $error='Invalid email or password, or teacher login has not been enabled yet.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Teacher Login | Student Portal</title><link rel="stylesheet" href="assets/auth.css"></head><body>
<div class="auth-shell"><aside class="auth-aside"><div class="aside-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div><div class="aside-copy"><span class="eyebrow">TEACHER SPACE</span><h1>Welcome, teacher.</h1><p>Sign in to access your teacher profile and class information.</p></div><div class="aside-bottom">Teaching and learning, connected.</div></aside>
<main class="auth-main"><section class="auth-card login-card"><div class="mobile-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div><span class="eyebrow">TEACHER ACCOUNT</span><h2>Teacher sign in</h2><p class="intro">Use the email and password assigned by your administrator.</p>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="field"><label>Email address</label><input type="email" name="email" placeholder="teacher@example.com" autocomplete="username" required></div><div class="field"><label>Password</label><input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required></div><button class="submit-btn">Sign In <span>→</span></button></form>
<p class="admin-link">Need access? Contact your administrator.</p><p class="switch-text">New teacher? <a href="teacher_register.php">Register here</a></p><p class="admin-link"><a href="index.php">← Back to role selection</a> · <a href="student_login.php">Student login</a></p></section></main></div></body></html>
