<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin();
$search=trim($_GET['search']??'');
if($search!==''){$stmt=$pdo->prepare('SELECT s.id,s.name,s.dept,u.phn_num FROM student s LEFT JOIN `user` u ON u.id=s.id WHERE s.id LIKE ? OR s.name LIKE ? OR s.dept LIKE ? OR u.phn_num LIKE ? ORDER BY s.id DESC');$q='%'.$search.'%';$stmt->execute([$q,$q,$q,$q]);$students=$stmt->fetchAll();}
else{$students=$pdo->query('SELECT s.id,s.name,s.dept,u.phn_num FROM student s LEFT JOIN `user` u ON u.id=s.id ORDER BY s.id DESC')->fetchAll();}
page_start('Students');admin_header('students');
?>
<main class="main-content"><header class="topbar"><div><span class="crumb">STUDENT MANAGEMENT</span><h1>Students</h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="page-intro"><div><h2>Student directory</h2><p>View registered student details.</p></div><form class="search-form" method="GET"><input name="search" value="<?= e($search) ?>" placeholder="Search ID, name, dept..."><button class="btn dark">Search</button></form></div>
<div class="panel"><div class="panel-head"><div><h3>All Students <span class="count-pill"><?= count($students) ?></span></h3></div></div><?php if(!$students): ?><p class="empty">No student records found.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>Department</th><th>Phone</th></tr></thead><tbody><?php foreach($students as $s): ?><tr><td><?= e($s['id']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['dept']) ?></td><td><?= e($s['phn_num']??'—') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div><?php page_end(); ?>
