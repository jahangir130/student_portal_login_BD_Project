<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin();

$students = current_admin_role() === 'Routine Manager' ? 0 : (int)$pdo->query('SELECT COUNT(*) FROM student')->fetchColumn();
$teachers = current_admin_role() === 'Routine Manager' ? 0 : (int)$pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
$directoryTeachers = current_admin_role() === 'Routine Manager' ? 0 : (int)$pdo->query('SELECT COUNT(*) FROM bubt_teacher_directory')->fetchColumn();
$directoryDepartments = current_admin_role() === 'Routine Manager' ? 0 : (int)$pdo->query('SELECT COUNT(*) FROM bubt_departments')->fetchColumn();
$admins = current_admin_role() === 'Super Admin' ? (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() : 0;
$routines = (int)$pdo->query('SELECT COUNT(*) FROM class_routines')->fetchColumn();
$pendingTeachers = 0;
if (current_admin_role() !== 'Routine Manager') { try { $pendingTeachers = (int)$pdo->query("SELECT COUNT(*) FROM teachers WHERE account_status='pending'")->fetchColumn(); } catch (PDOException $e) {} }
$departments = current_admin_role() === 'Routine Manager' ? 0 : (int)$pdo->query("SELECT COUNT(DISTINCT dept) FROM student WHERE TRIM(dept)<>''")->fetchColumn();
$recentStudents = current_admin_role() === 'Routine Manager' ? [] : $pdo->query('SELECT id,name,dept FROM student ORDER BY id DESC LIMIT 6')->fetchAll();
$recentTeachers = current_admin_role() === 'Routine Manager' ? [] : $pdo->query('SELECT teacher_id,name,department,designation,created_at FROM teachers ORDER BY teacher_id DESC LIMIT 6')->fetchAll();
$departmentRows = current_admin_role() === 'Routine Manager' ? [] : $pdo->query("SELECT dept, COUNT(*) AS total FROM student WHERE TRIM(dept)<>'' GROUP BY dept ORDER BY total DESC, dept ASC LIMIT 7")->fetchAll();
$maxDept = 1;
foreach ($departmentRows as $row) { $maxDept = max($maxDept, (int)$row['total']); }
$recentRoutines = $pdo->query('SELECT department,course_code,course_name,class_day,start_time,room_no FROM class_routines ORDER BY routine_id DESC LIMIT 5')->fetchAll();

page_start('Admin Dashboard'); admin_header('dashboard');
?>
<main class="main-content">
<header class="topbar"><div><span class="crumb">UNIVERSITY PORTAL / ADMINISTRATION</span><h1>Admin Dashboard</h1></div><div class="top-user"><span class="online-dot"></span><?= e($_SESSION['admin_name']) ?><span class="top-avatar"><?= e(strtoupper(substr($_SESSION['admin_name'] ?? 'A',0,1))) ?></span></div></header>
<section class="welcome-banner admin-welcome"><div><p>ADMINISTRATIVE OVERVIEW</p><h2>Welcome back, <?= e($_SESSION['admin_name']) ?> 👋</h2><span>Monitor student records, faculty, academic routines and portal activity from one place.</span></div><div class="banner-mark">BUBT</div></section>

<section class="admin-quick-actions">
  <?php if (current_admin_role() !== "Routine Manager"): ?><a class="quick-action" href="student_register.php"><span class="quick-icon qi-violet">＋</span><span><strong>Add Student</strong><small>Create a student record</small></span><b>→</b></a>
  <a class="quick-action" href="teacher_form.php"><span class="quick-icon qi-blue">＋</span><span><strong>Add Teacher</strong><small>Create faculty profile</small></span><b>→</b></a>
  <a class="quick-action" href="admin_bubt_directory.php"><span class="quick-icon qi-orange">BD</span><span><strong>BUBT Directory</strong><small>Departments &amp; faculty records</small></span><b>→</b></a>
  <?php endif; ?><a class="quick-action" href="routine_form.php"><span class="quick-icon qi-green">＋</span><span><strong>Create Routine</strong><small>Schedule a class</small></span><b>→</b></a>
  <a class="quick-action" href="routine_scan.php"><span class="quick-icon qi-orange">↥</span><span><strong>Import Routine</strong><small>Scan a routine image</small></span><b>→</b></a>
</section>

<section class="stats-grid admin-stats">
<?php if (current_admin_role() !== 'Routine Manager'): ?>
<div class="stat-card"><span class="stat-icon violet">ST</span><p>Total Students</p><strong><?= number_format($students) ?></strong><small>Student records in database</small></div>
<div class="stat-card"><span class="stat-icon blue">FC</span><p>Faculty Members</p><strong><?= number_format($teachers) ?></strong><small>Teacher profiles</small></div>
<div class="stat-card"><span class="stat-icon orange">DP</span><p>BUBT Departments</p><strong><?= number_format($directoryDepartments) ?></strong><small>Official department reference records</small></div>
<div class="stat-card"><span class="stat-icon green">FD</span><p>Directory Faculty</p><strong><?= number_format($directoryTeachers) ?></strong><small>Public faculty directory entries</small></div>
<?php endif; ?>
<div class="stat-card"><span class="stat-icon green">RT</span><p>Class Schedules</p><strong><?= number_format($routines) ?></strong><small>Routine entries created</small></div>
</section>

<section class="admin-insight-grid">
<?php if (current_admin_role() !== "Routine Manager"): ?><div class="panel department-panel"><div class="panel-head"><div><h3>Students by department</h3><p>Distribution based on current student records</p></div><a href="students.php">Manage students →</a></div>
<?php if (!$departmentRows): ?><p class="empty">No department data available yet.</p><?php else: ?><div class="department-bars"><?php foreach ($departmentRows as $d): $pct=(int)round(((int)$d['total']/$maxDept)*100); ?><div class="dept-bar-row"><div class="dept-bar-label"><span><?= e($d['dept']) ?></span><strong><?= (int)$d['total'] ?></strong></div><div class="dept-track"><span style="width:<?= $pct ?>%"></span></div></div><?php endforeach; ?></div><?php endif; ?>
</div>
<?php endif; ?><div class="panel attention-panel"><div class="panel-head"><div><h3>Needs Attention &amp; Overview</h3><p>Pending tasks and key administration totals</p></div><span class="attention-mark">!</span></div>
<?php if (current_admin_role() !== "Routine Manager"): ?><a class="attention-row attention-action" href="teachers.php?status=pending"><span class="attention-symbol">TC</span><span><strong>Pending teacher approvals</strong><small>Action required · Review registrations</small></span><b class="attention-count <?= $pendingTeachers>0?'count-alert':'count-clear' ?>"><?= number_format($pendingTeachers) ?></b></a><?php endif; ?>
<?php if (current_admin_role() === "Super Admin"): ?><a class="attention-row overview-row" href="admins.php"><span class="attention-symbol">AD</span><span><strong>Total admin accounts</strong><small>Overview · Manage administrator access</small></span><b class="attention-count"><?= number_format($admins) ?></b></a><?php endif; ?>
<a class="attention-row overview-row" href="routines.php"><span class="attention-symbol">CR</span><span><strong>Total class routine entries</strong><small>Overview · Open routine management</small></span><b class="attention-count"><?= number_format($routines) ?></b></a>
</div>
</section>

<?php if (current_admin_role() !== "Routine Manager"): ?><section class="content-grid admin-lower-grid">
<div class="panel"><div class="panel-head"><div><h3>Recently registered students</h3><p>Latest student records</p></div><a href="students.php">View all →</a></div><?php if(!$recentStudents): ?><p class="empty">No student records found yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>Department</th></tr></thead><tbody><?php foreach($recentStudents as $s): ?><tr><td><?= e($s['id']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['dept']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<div class="panel"><div class="panel-head"><div><h3>Recently added teachers</h3><p>Latest faculty profiles</p></div><a href="teachers.php">View all →</a></div><?php if(!$recentTeachers): ?><p class="empty">No teacher records found yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Name</th><th>Department</th><th>Status</th></tr></thead><tbody><?php foreach($recentTeachers as $t): $ts=$t['account_status']??'approved'; ?><tr><td><?= e($t['name']) ?></td><td><?= e($t['department']) ?></td><td><span class="status-chip <?= $ts==='pending'?'status-pending':'status-approved' ?>"><?= e(ucfirst($ts)) ?></span></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
</section>
<?php endif; ?><section class="panel recent-routine-panel"><div class="panel-head"><div><h3>Recently created class routines</h3><p>Latest academic scheduling entries</p></div><a href="routines.php">Manage routines →</a></div>
<?php if(!$recentRoutines): ?><p class="empty">No routine entries found yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Department</th><th>Course</th><th>Day</th><th>Time</th><th>Room</th></tr></thead><tbody><?php foreach($recentRoutines as $r): ?><tr><td><?= e($r['department']) ?></td><td><?= e($r['course_code'].' · '.$r['course_name']) ?></td><td><?= e($r['class_day']) ?></td><td><?= e(date('g:i A',strtotime($r['start_time']))) ?></td><td><?= e($r['room_no']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<p class="copyright">BUBT Student Portal · Administrative workspace</p></main>
<?php page_end(); ?>
