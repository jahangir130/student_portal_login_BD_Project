<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(!empty($_SESSION['teacher_id'])){header('Location: teacher_dashboard.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $email=trim($_POST['email']??'');
 $password=$_POST['password']??'';
 $st=$pdo->prepare('SELECT teacher_id,name,email,department,designation,password,account_status FROM teachers WHERE email=? LIMIT 1');
 $st->execute([$email]);
 $teacher=$st->fetch();

 if(!$teacher){
  $error='No teacher account was found with this email address.';
 }elseif(($teacher['account_status']??'approved')==='pending'){
  $error='Your registration is waiting for administrator approval. Please try again after approval.';
 }elseif(empty($teacher['password'])){
  $error='Your teacher profile exists, but login has not been enabled. Please contact the administrator to set your password.';
 }elseif(($teacher['account_status']??'approved')!=='approved'){
  $error='Your teacher account is not active. Please contact the administrator.';
 }elseif(!password_verify($password,$teacher['password'])){
  $error='Incorrect password. Please check your password and try again.';
 }else{
  session_regenerate_id(true);
  $_SESSION['teacher_id']=$teacher['teacher_id'];
  $_SESSION['teacher_name']=$teacher['name'];
  header('Location: teacher_dashboard.php');
  exit;
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#102d46">
<title>Teacher Login | BUBT Student Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#102d46;--teal:#087f8c;--teal2:#0da49d;--ink:#172b3a;--muted:#718096;--line:#e1e8ef;--purple:#5b50e8}
*{box-sizing:border-box}
body.teacher-auth{margin:0;min-height:100vh;font-family:Inter,Arial,sans-serif;color:var(--ink);background:#f2f5fa;display:flex;flex-direction:column}
.auth-top{height:76px;padding:0 clamp(20px,5vw,76px);display:flex;align-items:center;justify-content:space-between;background:#fff;border-bottom:1px solid #e8edf3}
.auth-brand{display:flex;align-items:center;gap:12px;color:var(--navy);text-decoration:none}
.auth-brand img{width:46px;height:46px;object-fit:contain;border-radius:12px;background:#fff;border:1px solid #edf0f4;padding:3px}
.auth-brand strong{display:block;font-size:12px;letter-spacing:.7px}.auth-brand small{display:block;margin-top:4px;color:#8793a4;font-size:9px;letter-spacing:1.2px;font-weight:700}
.top-links{display:flex;gap:20px;align-items:center}.top-links a{font-size:11px;font-weight:700;color:#617187;text-decoration:none}.top-links a:hover{color:var(--teal)}
.auth-layout{width:min(1120px,calc(100% - 48px));margin:auto;display:grid;grid-template-columns:1fr minmax(390px,.82fr);gap:clamp(35px,7vw,100px);align-items:center;padding:54px 0}
.auth-intro{padding:20px 0}.eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--teal);font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase}
.eyebrow:before{content:"";width:22px;height:2px;background:var(--teal);border-radius:2px}
.auth-intro h1{font-size:clamp(34px,4.5vw,54px);line-height:1.08;letter-spacing:-2.2px;color:var(--navy);margin:18px 0 15px;max-width:570px}
.auth-intro>p{font-size:14px;line-height:1.85;color:#6b7b8d;max-width:490px;margin:0}
.portal-points{display:grid;gap:14px;margin-top:34px;max-width:470px}
.portal-point{display:flex;align-items:flex-start;gap:12px}
.point-icon{width:34px;height:34px;display:grid;place-items:center;flex:none;border-radius:10px;background:#e4f4f3;color:var(--teal);font-size:15px;font-weight:800}
.portal-point strong{display:block;font-size:12px;color:#30465a;margin:1px 0 4px}.portal-point small{display:block;font-size:10px;color:#8794a4;line-height:1.6}
.role-list{display:flex;gap:8px;flex-wrap:wrap;margin-top:27px}
.role-pill{padding:7px 10px;border:1px solid #dfe7ee;border-radius:30px;background:#fff;color:#607187;font-size:9px;font-weight:700}
.login-card{width:100%;max-width:455px;justify-self:end;background:#fff;border:1px solid #e4eaf0;border-radius:22px;padding:clamp(26px,4vw,40px);box-shadow:0 22px 65px #20384e12}
.card-mark{width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#087f8c,#10a39b);display:grid;place-items:center;color:white;font-size:14px;font-weight:800;letter-spacing:-.5px;box-shadow:0 8px 18px #087f8c2b;margin-bottom:24px}
.card-eyebrow{font-size:9px;letter-spacing:1.8px;font-weight:800;color:#8793a4;margin:0 0 9px}
.login-card h2{font-size:27px;letter-spacing:-1px;color:var(--navy);margin:0 0 8px}
.card-subtitle{font-size:11px;color:#8793a4;line-height:1.7;margin:0 0 24px}
.form-field{margin:0 0 17px}.form-field label{display:block;font-size:10px;font-weight:700;color:#46596d;margin-bottom:8px}
.form-field input{display:block;width:100%;height:46px;padding:0 13px;border:1px solid #d8e1e9;border-radius:9px;background:#fbfcfe;color:#1e3347;font:12px Inter,Arial;outline:none;transition:border .2s,box-shadow .2s}
.form-field input:focus{border-color:#168e99;background:#fff;box-shadow:0 0 0 4px #0b8b9617}
.login-card .password-visibility-wrap input{padding-right:68px}
.login-submit{width:100%;height:47px;border:0;border-radius:10px;background:linear-gradient(100deg,#087f8c,#10a39b);color:#fff;font:700 12px Inter,Arial;cursor:pointer;box-shadow:0 8px 18px #087f8c2b;transition:transform .18s,filter .18s;margin-top:5px}
.login-submit:hover{filter:brightness(1.05);transform:translateY(-1px)}.login-submit:active{transform:translateY(0)}
.login-error{display:flex;gap:9px;align-items:flex-start;padding:12px 13px;margin:0 0 18px;border:1px solid #ffd3cf;background:#fff4f2;border-radius:9px;color:#b42318;font-size:10px;line-height:1.6}
.error-symbol{width:18px;height:18px;border-radius:50%;background:#ffe0dc;display:grid;place-items:center;flex:none;font-weight:800}
.login-divider{height:1px;background:#edf0f4;margin:22px 0 17px}
.registration-note{text-align:center;color:#8793a4;font-size:10px;line-height:1.8;margin:0}
.registration-note a,.back-links a{color:var(--teal);font-weight:700;text-decoration:none}
.registration-note a:hover,.back-links a:hover{text-decoration:underline}
.back-links{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;margin-top:15px}
.back-links a{font-size:9px;color:#8a96a6}
.secure-note{display:flex;justify-content:center;align-items:center;gap:6px;margin-top:22px;color:#a0aab7;font-size:9px}
.secure-note span{color:#159568;font-size:12px}
.auth-footer{text-align:center;padding:0 20px 24px;color:#9aa5b3;font-size:9px}
.password-visibility-wrap{position:relative;display:block;width:100%}
.password-visibility-wrap input{width:100%;padding-right:68px!important;box-sizing:border-box}
.password-visibility-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--teal);font:700 10px Inter,Arial;cursor:pointer;padding:7px;z-index:2}
.password-visibility-toggle:focus{outline:2px solid #79c9c7;outline-offset:2px;border-radius:5px}
@media(max-width:850px){.auth-layout{grid-template-columns:1fr;max-width:520px;width:calc(100% - 32px);gap:22px;padding:35px 0}.auth-intro{padding:0}.auth-intro h1{font-size:35px}.auth-intro>p{font-size:12px}.portal-points{display:none}.role-list{margin-top:17px}.login-card{justify-self:stretch;max-width:none}}
@media(max-width:520px){.auth-top{height:66px;padding:0 16px}.auth-brand img{width:39px;height:39px}.auth-brand strong{font-size:10px}.auth-brand small{font-size:8px}.top-links{gap:10px}.top-links a{font-size:9px}.auth-layout{padding:25px 0}.auth-intro h1{font-size:30px;letter-spacing:-1.2px}.login-card{padding:25px 20px;border-radius:17px}.login-card h2{font-size:24px}.role-pill{font-size:8px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important;scroll-behavior:auto!important}}
</style>
</head>
<body class="teacher-auth">
<header class="auth-top">
  <a class="auth-brand" href="index.php">
    <img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt="BUBT logo">
    <span><strong>BUBT STUDENT PORTAL</strong><small>FACULTY &amp; TEACHING</small></span>
  </a>
  <nav class="top-links" aria-label="Portal links">
    <a href="admin_login.php">Admin</a>
    <a href="student_login.php">Student</a>
  </nav>
</header>

<main class="auth-layout">
  <section class="auth-intro">
    <span class="eyebrow">Faculty workspace</span>
    <h1>Teaching, connected.</h1>
    <p>Sign in to access your faculty workspace, teaching profile and assigned class information in the BUBT Student Portal.</p>
    <div class="portal-points">
      <div class="portal-point"><span class="point-icon">✓</span><span><strong>Your teaching workspace</strong><small>Access your teacher profile and class-related information from one place.</small></span></div>
      <div class="portal-point"><span class="point-icon">▦</span><span><strong>Class routine access</strong><small>Review your assigned classes and teaching schedule after signing in.</small></span></div>
      <div class="portal-point"><span class="point-icon">⌁</span><span><strong>Administrator-approved access</strong><small>New teacher registrations become active after administrator approval.</small></span></div>
    </div>
    <div class="role-list"><span class="role-pill">Faculty Profile</span><span class="role-pill">Class Routine</span><span class="role-pill">Academic Workspace</span></div>
  </section>

  <section class="login-card" aria-labelledby="login-title">
    <div class="card-mark" aria-hidden="true">TC</div>
    <p class="card-eyebrow">TEACHER ACCOUNT</p>
    <h2 id="login-title">Welcome back</h2>
    <p class="card-subtitle">Use the email address and password linked to your approved teacher account.</p>
    <?php if($error): ?><div class="login-error" role="alert"><span class="error-symbol">!</span><span><?= e($error) ?></span></div><?php endif; ?>
    <form method="POST" action="teacher_login.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="form-field">
        <label for="teacher-email">Email address</label>
        <input id="teacher-email" type="email" name="email" placeholder="you@bubt.edu.bd" autocomplete="username" maxlength="150" required>
      </div>
      <div class="form-field">
        <label for="teacher-password">Password</label>
        <input id="teacher-password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
      </div>
      <button class="login-submit" type="submit">Sign in to Teacher Portal <span aria-hidden="true">→</span></button>
    </form>
    <div class="login-divider"></div>
    <p class="registration-note">New teacher? <a href="teacher_register.php">Register here</a><br>Already registered? Your account must be approved by an administrator before sign in.</p>
    <div class="back-links"><a href="index.php">← Role selection</a><a href="admin_login.php">Admin login</a><a href="student_login.php">Student login</a></div>
    <div class="secure-note"><span>●</span> Secure faculty access</div>
  </section>
</main>
<footer class="auth-footer">Bangladesh University of Business &amp; Technology · Student Portal</footer>
<script src="assets/password-toggle.js"></script>
</body>
</html>
