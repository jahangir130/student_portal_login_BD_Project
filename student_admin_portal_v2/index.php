<?php
session_start();
if (!empty($_SESSION['admin_id'])) { header('Location: admin_dashboard.php'); exit; }
if (!empty($_SESSION['student_id'])) { header('Location: student_dashboard.php'); exit; }
if (!empty($_SESSION['teacher_id'])) { header('Location: teacher_dashboard.php'); exit; }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Portal | Choose Login</title><link rel="stylesheet" href="assets/auth.css">
<style>
.portal-home{min-height:100vh;padding:34px 20px 50px;background:radial-gradient(circle at 10% 5%,#d9f0ef 0,transparent 35%),#f3f7f8}
.portal-wrap{max-width:1100px;margin:auto}
.portal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:70px}
.portal-logo{display:flex;align-items:center;gap:12px;color:#12304a;font-size:13px;font-weight:800;letter-spacing:1.2px}
.portal-logo img{width:50px;height:50px;border-radius:14px;object-fit:cover;border:1px solid #d9e6e8}
.portal-tag{font-size:10px;color:#718391}
.hero{text-align:center;max-width:680px;margin:0 auto 38px}
.hero .eyebrow{display:inline-block;padding:8px 12px;border-radius:30px;background:#e0f3f1}
.hero h1{font-size:clamp(30px,5vw,48px);letter-spacing:-1.8px;color:#12304a;margin:17px 0 10px}
.hero p{font-size:13px;color:#728394;line-height:1.8;margin:0}
.role-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.role-card{background:#fff;border:1px solid #e0e9eb;border-radius:20px;padding:27px 24px;box-shadow:0 14px 40px #183d4b0b;display:flex;flex-direction:column;min-height:260px;transition:.2s}
.role-card:hover{transform:translateY(-4px);box-shadow:0 18px 45px #183d4b16}
.role-icon{width:52px;height:52px;border-radius:15px;background:#e6f5f3;color:#087f8c;display:grid;place-items:center;font-size:23px;font-weight:800}
.role-card h2{font-size:20px;color:#12304a;margin:20px 0 7px}
.role-card p{font-size:11px;color:#788995;line-height:1.7;margin:0 0 22px;flex:1}
.role-link{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(100deg,#087f8c,#10a39b);color:white;border-radius:10px;padding:12px 14px;font-size:11px;font-weight:700}
.role-link:hover{text-decoration:none;filter:brightness(1.05)}
.portal-footer{text-align:center;color:#99a7ae;font-size:10px;margin-top:42px}
@media(max-width:760px){.portal-header{margin-bottom:48px}.role-grid{grid-template-columns:1fr;max-width:480px;margin:auto}.role-card{min-height:220px}.portal-tag{display:none}}
</style></head><body class="portal-home">
<div class="portal-wrap">
<header class="portal-header"><a class="portal-logo" href="index.php"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></a><span class="portal-tag">UNIVERSITY MANAGEMENT SYSTEM</span></header>
<section class="hero"><span class="eyebrow">WELCOME TO YOUR CAMPUS</span><h1>One portal. Three ways in.</h1><p>Select your account type to continue to the right sign-in page.</p></section>
<section class="role-grid">
<article class="role-card"><div class="role-icon">S</div><h2>Student Login</h2><p>Access your student profile, academic information and published class routine.</p><a class="role-link" href="student_login.php">Continue as Student <span>→</span></a></article>
<article class="role-card"><div class="role-icon">T</div><h2>Teacher Login</h2><p>Sign in to view your teacher profile and class schedule. Teacher access is created by an administrator.</p><a class="role-link" href="teacher_login.php">Continue as Teacher <span>→</span></a></article>
<article class="role-card"><div class="role-icon">A</div><h2>Admin Login</h2><p>Manage students, teachers, administrators and class routines from the control panel.</p><a class="role-link" href="admin_login.php">Continue as Admin <span>→</span></a></article>
</section>
<footer class="portal-footer">© <?= date('Y') ?> Student Portal · Secure role-based entry</footer>
</div></body></html>
