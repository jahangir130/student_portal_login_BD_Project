<?php
session_start();
if (!empty($_SESSION['admin_id'])) { header('Location: admin_dashboard.php'); exit; }
if (!empty($_SESSION['student_id'])) { header('Location: student_dashboard.php'); exit; }
if (!empty($_SESSION['teacher_id'])) { header('Location: teacher_dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#102d46">
<title>BUBT Student Portal | Choose Your Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--navy:#102d46;--teal:#087f8c;--teal2:#10a39b;--ink:#1b3042;--muted:#718096;--line:#e3eaf0}
*{box-sizing:border-box}
body.portal-home{margin:0;min-height:100vh;font-family:Inter,Arial,sans-serif;color:var(--ink);background:radial-gradient(ellipse at 12% 8%,#dcefed 0,transparent 34%),radial-gradient(ellipse at 90% 90%,#e8edff 0,transparent 34%),#f4f7fa}
.portal-wrap{width:min(1120px,calc(100% - 48px));margin:0 auto;padding:27px 0 24px}
.portal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:70px}
.portal-logo{display:flex;align-items:center;gap:14px;color:var(--navy);text-decoration:none;min-width:0}
.logo-frame{width:68px;height:68px;border-radius:16px;background:#fff;border:1px solid #e2e9ee;box-shadow:0 5px 18px #17384d0c;display:grid;place-items:center;padding:7px;flex:none;overflow:hidden}
.logo-frame img{width:100%;height:100%;object-fit:contain;display:block}
.logo-fallback{display:none;width:100%;height:100%;border-radius:10px;background:#f0f5f8;color:var(--navy);font-weight:800;font-size:18px;align-items:center;justify-content:center}
.brand-copy strong{display:block;font-size:13px;letter-spacing:1px;font-weight:800}
.brand-copy small{display:block;margin-top:5px;color:#8795a2;font-size:9px;letter-spacing:1.7px;font-weight:700}
.portal-tag{display:flex;align-items:center;gap:8px;color:#728394;font-size:9px;letter-spacing:1.4px;font-weight:800}
.portal-tag:before{content:"";width:7px;height:7px;border-radius:50%;background:#16a078;box-shadow:0 0 0 4px #16a07818}
.hero{text-align:center;max-width:700px;margin:0 auto 42px}
.eyebrow{display:inline-flex;align-items:center;gap:8px;padding:8px 13px;border-radius:30px;background:#e3f3f1;color:var(--teal);font-size:9px;font-weight:800;letter-spacing:1.8px}
.eyebrow:before{content:"";width:6px;height:6px;border-radius:50%;background:var(--teal)}
.hero h1{font-size:clamp(32px,5vw,52px);line-height:1.08;letter-spacing:-2px;color:var(--navy);margin:19px 0 12px}
.hero p{font-size:13px;color:var(--muted);line-height:1.8;margin:0}
.role-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.role-card{position:relative;overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:19px;padding:26px 24px 22px;box-shadow:0 13px 35px #1b35470a;display:flex;flex-direction:column;min-height:275px;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.role-card:before{content:"";position:absolute;inset:0 0 auto;height:3px;background:linear-gradient(90deg,var(--teal),#56b5b0);opacity:.85}
.role-card:hover{transform:translateY(-4px);box-shadow:0 20px 42px #1b354714;border-color:#cbdedc}
.role-card:focus-within{outline:3px solid #0c8b951f;outline-offset:2px}
.role-icon{width:49px;height:49px;border-radius:14px;background:#e8f5f3;color:var(--teal);display:grid;place-items:center;font-size:19px;font-weight:800}
.role-card:nth-child(2) .role-icon{background:#eaf1ff;color:#4269d9}
.role-card:nth-child(3) .role-icon{background:#f0edff;color:#6256dc}
.role-kicker{margin:19px 0 5px;color:#9aa5b1;font-size:8px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase}
.role-card h2{font-size:19px;color:var(--navy);margin:0 0 8px;letter-spacing:-.4px}
.role-card p{font-size:10px;color:#788995;line-height:1.75;margin:0 0 21px;flex:1}
.role-link{display:flex;align-items:center;justify-content:space-between;gap:10px;background:var(--teal);color:white;border-radius:9px;padding:12px 13px;font-size:10px;font-weight:700;text-decoration:none;transition:filter .2s,transform .2s}
.role-card:nth-child(2) .role-link{background:#4269d9}
.role-card:nth-child(3) .role-link{background:#6256dc}
.role-link:hover{filter:brightness(1.08);transform:translateY(-1px)}
.role-link span{font-size:15px;line-height:1}
.portal-footer{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;color:#9aa6b2;font-size:9px;margin-top:43px;padding-top:17px;border-top:1px solid #e4eaf0}
@media(max-width:800px){.portal-header{margin-bottom:48px}.role-grid{grid-template-columns:1fr;max-width:520px;margin:auto}.role-card{min-height:230px}.hero{margin-bottom:30px}}
@media(max-width:520px){.portal-wrap{width:calc(100% - 30px);padding-top:16px}.portal-header{margin-bottom:37px}.logo-frame{width:55px;height:55px;border-radius:13px}.brand-copy strong{font-size:10px}.brand-copy small{font-size:7px;letter-spacing:1.1px}.portal-tag{font-size:0}.portal-tag:before{width:8px;height:8px}.hero h1{font-size:34px;letter-spacing:-1.3px}.hero p{font-size:11px}.role-card{padding:22px 20px}.portal-footer{margin-top:30px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important}}
</style>
</head>
<body class="portal-home">
<div class="portal-wrap">
<header class="portal-header">
  <a class="portal-logo" href="index.php" aria-label="BUBT Student Portal home">
    <span class="logo-frame">
      <img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt="Bangladesh University of Business and Technology logo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <span class="logo-fallback" aria-hidden="true">BUBT</span>
    </span>
    <span class="brand-copy"><strong>BUBT STUDENT PORTAL</strong><small>UNIVERSITY MANAGEMENT SYSTEM</small></span>
  </a>
  <span class="portal-tag">SECURE PORTAL ACCESS</span>
</header>
<section class="hero">
  <span class="eyebrow">WELCOME TO YOUR CAMPUS</span>
  <h1>One portal. Three ways in.</h1>
  <p>Select your account type to continue to the right sign-in page.</p>
</section>
<section class="role-grid" aria-label="Choose your portal account">
  <article class="role-card">
    <div class="role-icon" aria-hidden="true">S</div><span class="role-kicker">Academic access</span>
    <h2>Student Login</h2><p>Access your student profile, academic information, faculty directory and published class routine.</p>
    <a class="role-link" href="student_login.php">Continue as Student <span aria-hidden="true">→</span></a>
  </article>
  <article class="role-card">
    <div class="role-icon" aria-hidden="true">T</div><span class="role-kicker">Faculty access</span>
    <h2>Teacher Login</h2><p>Sign in to view your teacher profile and assigned class schedule. New accounts require administrator approval.</p>
    <a class="role-link" href="teacher_login.php">Continue as Teacher <span aria-hidden="true">→</span></a>
  </article>
  <article class="role-card">
    <div class="role-icon" aria-hidden="true">A</div><span class="role-kicker">System access</span>
    <h2>Admin Login</h2><p>Manage student records, teacher accounts, academic routines and portal administration according to your role.</p>
    <a class="role-link" href="admin_login.php">Continue as Admin <span aria-hidden="true">→</span></a>
  </article>
</section>
<footer class="portal-footer"><span>© <?= date('Y') ?> BUBT Student Portal</span><span>Secure role-based entry</span></footer>
</div>
</body>
</html>
