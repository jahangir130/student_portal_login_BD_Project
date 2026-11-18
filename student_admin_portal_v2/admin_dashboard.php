<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin();
$students = (int)$pdo->query('SELECT COUNT(*) FROM student')->fetchColumn();
$teachers = (int)$pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
$admins = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$departments = (int)$pdo->query('SELECT COUNT(DISTINCT dept) FROM student WHERE dept<>""')->fetchColumn();
$recentStudents = $pdo->query('SELECT id,name,dept FROM student ORDER BY id DESC LIMIT 5')->fetchAll();
$recentTeachers = $pdo->query('SELECT teacher_id,name,department,designation FROM teachers ORDER BY teacher_id DESC LIMIT 5')->fetchAll();
page_start('Dashboard'); admin_header('dashboard');
?>
<main class="main-content"><header class="topbar"><div><span class="crumb">OVERVIEW</span><h1>Dashboard</h1></div><div class="top-user"><span class="online-dot"></span><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<section class="welcome-banner"><div><p>ADMIN CONTROL CENTER</p><h2>Welcome back, <?= e($_SESSION['admin_name']) ?> 👋</h2><span>Here is what's happening in your portal today.</span></div><div class="banner-mark">SP</div></section>
<section class="stats-grid">
<div class="stat-card"><span class="stat-icon violet">ST</span><p>Total Students</p><strong><?= $students ?></strong><small>Registered student records</small></div>
<div class="stat-card"><span class="stat-icon blue">TC</span><p>Total Teachers</p><strong><?= $teachers ?></strong><small>Teacher profiles</small></div>
<div class="stat-card"><span class="stat-icon green">AD</span><p>Total Admins</p><strong><?= $admins ?></strong><small>Administrator accounts</small></div>
<div class="stat-card"><span class="stat-icon orange">DP</span><p>Departments</p><strong><?= $departments ?></strong><small>Student departments</small></div>
</section>
<section class="content-grid">
<div class="panel"><div class="panel-head"><div><h3>Recently registered students</h3><p>Latest student records</p></div><a href="students.php">View all →</a></div><?php if(!$recentStudents): ?><p class="empty">No student records found yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>Department</th></tr></thead><tbody><?php foreach($recentStudents as $s): ?><tr><td><?= e($s['id']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['dept']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<div class="panel"><div class="panel-head"><div><h3>Recently added teachers</h3><p>Latest teacher profiles</p></div><a href="teachers.php">View all →</a></div><?php if(!$recentTeachers): ?><p class="empty">No teacher records found yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Name</th><th>Department</th><th>Designation</th></tr></thead><tbody><?php foreach($recentTeachers as $t): ?><tr><td><?= e($t['name']) ?></td><td><?= e($t['department']) ?></td><td><?= e($t['designation']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
</section><p class="copyright">Student Portal Admin · Management dashboard</p></main>
<?php page_end(); ?>
