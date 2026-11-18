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

    $roleMap = [
        'admin' => 'Admin',
        'routine manager' => 'Routine Manager',
        'super admin' => 'Super Admin'
    ];
    $storedRole = strtolower(trim((string)($admin['role'] ?? '')));
    if ($admin && isset($roleMap[$storedRole]) && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $roleMap[$storedRole];
        header('Location: admin_dashboard.php');
        exit;
    }
    $error = 'Invalid email/password or unsupported admin role. Please contact the Super Admin.';
}

$adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#102d46">
<title>Admin Login | BUBT Student Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#102d46;--teal:#087f8c;--teal2:#0da49d;--ink:#172b3a;--muted:#718096;--line:#e1e8ef;--purple:#5b50e8}
*{box-sizing:border-box}
body.admin-auth{margin:0;min-height:100vh;font-family:Inter,Arial,sans-serif;color:var(--ink);background:#f2f5fa;display:flex;flex-direction:column}
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
.card-mark{width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#5c55ed,#267fc0);display:grid;place-items:center;color:white;font-size:14px;font-weight:800;letter-spacing:-.5px;box-shadow:0 8px 18px #5b50e82b;margin-bottom:24px}
.card-eyebrow{font-size:9px;letter-spacing:1.8px;font-weight:800;color:#8793a4;margin:0 0 9px}
.login-card h2{font-size:27px;letter-spacing:-1px;color:var(--navy);margin:0 0 8px}
.card-subtitle{font-size:11px;color:#8793a4;line-height:1.7;margin:0 0 24px}
.form-field{margin:0 0 17px}.form-field label{display:block;font-size:10px;font-weight:700;color:#46596d;margin-bottom:8px}
.form-field input{display:block;width:100%;height:46px;padding:0 13px;border:1px solid #d8e1e9;border-radius:9px;background:#fbfcfe;color:#1e3347;font:12px Inter,Arial;outline:none;transition:border .2s,box-shadow .2s}
.form-field input:focus{border-color:#168e99;background:#fff;box-shadow:0 0 0 4px #0b8b9617}
.login-card .password-visibility-wrap input{padding-right:68px}
.login-submit{width:100%;height:47px;border:0;border-radius:10px;background:linear-gradient(100deg,#5148dc,#6559ef);color:#fff;font:700 12px Inter,Arial;cursor:pointer;box-shadow:0 8px 18px #5b50e82b;transition:transform .18s,filter .18s;margin-top:5px}
.login-submit:hover{filter:brightness(1.05);transform:translateY(-1px)}.login-submit:active{transform:translateY(0)}
.login-error{display:flex;gap:9px;align-items:flex-start;padding:12px 13px;margin:0 0 18px;border:1px solid #ffd3cf;background:#fff4f2;border-radius:9px;color:#b42318;font-size:10px;line-height:1.6}
.error-symbol{width:18px;height:18px;border-radius:50%;background:#ffe0dc;display:grid;place-items:center;flex:none;font-weight:800}
.login-divider{height:1px;background:#edf0f4;margin:22px 0 17px}
.registration-note{text-align:center;color:#8793a4;font-size:10px;line-height:1.8;margin:0}
.registration-note a,.back-links a{color:var(--purple);font-weight:700;text-decoration:none}
.registration-note a:hover,.back-links a:hover{text-decoration:underline}
.back-links{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;margin-top:15px}
.back-links a{font-size:9px;color:#8a96a6}
.secure-note{display:flex;justify-content:center;align-items:center;gap:6px;margin-top:22px;color:#a0aab7;font-size:9px}
.secure-note span{color:#159568;font-size:12px}
.auth-footer{text-align:center;padding:0 20px 24px;color:#9aa5b3;font-size:9px}
.password-visibility-wrap{position:relative;display:block;width:100%}
.password-visibility-wrap input{width:100%;padding-right:68px!important;box-sizing:border-box}
.password-visibility-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--purple);font:700 10px Inter,Arial;cursor:pointer;padding:7px;z-index:2}
.password-visibility-toggle:focus{outline:2px solid #aaa4ff;outline-offset:2px;border-radius:5px}
@media(max-width:850px){.auth-layout{grid-template-columns:1fr;max-width:520px;width:calc(100% - 32px);gap:22px;padding:35px 0}.auth-intro{padding:0}.auth-intro h1{font-size:35px}.auth-intro>p{font-size:12px}.portal-points{display:none}.role-list{margin-top:17px}.login-card{justify-self:stretch;max-width:none}}
@media(max-width:520px){.auth-top{height:66px;padding:0 16px}.auth-brand img{width:39px;height:39px}.auth-brand strong{font-size:10px}.auth-brand small{font-size:8px}.top-links{gap:10px}.top-links a{font-size:9px}.auth-layout{padding:25px 0}.auth-intro h1{font-size:30px;letter-spacing:-1.2px}.login-card{padding:25px 20px;border-radius:17px}.login-card h2{font-size:24px}.role-pill{font-size:8px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important;scroll-behavior:auto!important}}
</style>
</head>
<body class="admin-auth">
<header class="auth-top">
  <a class="auth-brand" href="index.php">
    <img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt="BUBT logo">
    <span><strong>BUBT STUDENT PORTAL</strong><small>ADMINISTRATION</small></span>
  </a>
  <nav class="top-links" aria-label="Portal links">
    <a href="teacher_login.php">Teacher</a>
    <a href="student_login.php">Student</a>
  </nav>
</header>

<main class="auth-layout">
  <section class="auth-intro">
    <span class="eyebrow">Secure administration</span>
    <h1>Manage your campus, with confidence.</h1>
    <p>Sign in to access the BUBT Student Portal administration workspace. Your account permissions will be applied automatically after login.</p>
    <div class="portal-points">
      <div class="portal-point"><span class="point-icon">✓</span><span><strong>One secure sign-in</strong><small>All administrator accounts use the same login page and their own registered credentials.</small></span></div>
      <div class="portal-point"><span class="point-icon">▦</span><span><strong>Role-based workspace</strong><small>Dashboard access is tailored to your administrator role and responsibilities.</small></span></div>
      <div class="portal-point"><span class="point-icon">⌁</span><span><strong>Academic operations</strong><small>Manage student records, faculty information and class routines according to your access.</small></span></div>
    </div>
    <div class="role-list" aria-label="Available administrator roles">
      <span class="role-pill">Super Admin</span><span class="role-pill">Admin</span><span class="role-pill">Routine Manager</span>
    </div>
  </section>

  <section class="login-card" aria-labelledby="login-title">
    <div class="card-mark" aria-hidden="true">SP</div>
    <p class="card-eyebrow">ADMINISTRATION PORTAL</p>
    <h2 id="login-title">Welcome back</h2>
    <p class="card-subtitle">Enter your administrator credentials to continue.</p>
    <?php if ($error): ?><div class="login-error" role="alert"><span class="error-symbol">!</span><span><?= e($error) ?></span></div><?php endif; ?>
    <form method="POST" action="admin_login.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="form-field">
        <label for="admin-email">Email address</label>
        <input id="admin-email" type="email" name="email" placeholder="you@example.com" autocomplete="username" maxlength="150" required>
      </div>
      <div class="form-field">
        <label for="admin-password">Password</label>
        <input id="admin-password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
      </div>
      <button class="login-submit" type="submit">Sign in to Dashboard <span aria-hidden="true">→</span></button>
    </form>
    <div class="login-divider"></div>
    <?php if ($adminCount === 0): ?>
      <p class="registration-note">First time setting up the portal? <a href="setup_admin.php">Create the first Super Admin</a></p>
    <?php else: ?>
      <p class="registration-note">Need an administrator account? <a href="admin_register.php">View registration instructions</a></p>
    <?php endif; ?>
    <div class="back-links"><a href="index.php">← Role selection</a><a href="teacher_login.php">Teacher login</a><a href="student_login.php">Student login</a></div>
    <div class="secure-note"><span>●</span> Secure administrator access</div>
  </section>
</main>
<footer class="auth-footer">Bangladesh University of Business &amp; Technology · Student Portal</footer>
<script src="assets/password-toggle.js"></script>
</body>
</html>
