<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin();
$admins=$pdo->query('SELECT admin_id,name,email,phone,role,created_at FROM admins ORDER BY admin_id')->fetchAll();
page_start('Admin Panel');admin_header('admins');
?>
<main class="main-content"><header class="topbar"><div><span class="crumb">ACCESS MANAGEMENT</span><h1>Admin Panel</h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="page-intro"><div><h2>Administrator accounts</h2><p>View administrator details and add another admin account.</p></div><a class="btn primary" href="admin_form.php">＋ Add Admin</a></div>
<div class="panel"><div class="panel-head"><div><h3>All Administrators <span class="count-pill"><?= count($admins) ?></span></h3><p>Accounts with access to the admin portal</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Admin</th><th>Email</th><th>Phone</th><th>Role</th><th>Created</th></tr></thead><tbody><?php foreach($admins as $a): ?><tr><td><div class="person-cell"><span class="person-avatar"><?= e(strtoupper(substr($a['name'],0,1))) ?></span><strong><?= e($a['name']) ?></strong></div><small class="sub-id">Admin #<?= (int)$a['admin_id'] ?></small></td><td><?= e($a['email']) ?></td><td><?= e($a['phone']) ?></td><td><span class="tag"><?= e($a['role']) ?></span></td><td><?= e(date('d M Y',strtotime($a['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<p class="hint">For safety, this version does not provide admin deletion from the browser. Only a Super Admin should manage administrator access.</p><?php page_end(); ?>
