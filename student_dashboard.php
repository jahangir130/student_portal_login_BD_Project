<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
if (empty($_SESSION['student_id'])) { header('Location: student_login.php'); exit; }

$st = $pdo->prepare('SELECT s.id, s.name, s.dept, u.phn_num FROM student s JOIN `user` u ON u.id=s.id WHERE s.id=?');
$st->execute([$_SESSION['student_id']]);
$student = $st->fetch();
if (!$student) { session_destroy(); header('Location: student_login.php'); exit; }

$records = [];
$semesters = [];
$selectedSemester = trim($_GET['semester'] ?? '');
$selectedYear = trim($_GET['year'] ?? '');
$academicDataReady = true;
try {
    $q = $pdo->prepare("SELECT DISTINCT semester, academic_year FROM student_course_results WHERE student_id=? ORDER BY academic_year DESC, semester");
    $q->execute([$student['id']]);
    $semesters = $q->fetchAll();
    $sql = "SELECT * FROM student_course_results WHERE student_id=?";
    $params = [$student['id']];
    if ($selectedSemester !== '') { $sql .= " AND semester=?"; $params[] = $selectedSemester; }
    if ($selectedYear !== '') { $sql .= " AND academic_year=?"; $params[] = $selectedYear; }
    $sql .= " ORDER BY academic_year DESC, semester, course_code";
    $q = $pdo->prepare($sql); $q->execute($params); $records = $q->fetchAll();
} catch (PDOException $e) {
    $academicDataReady = false;
}
$years = array_values(array_unique(array_map(fn($r) => (string)$r['academic_year'], $semesters)));
$semesterMonths = 4;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Dashboard</title>
<link rel="stylesheet" href="assets/auth.css">
<style>
.student-page{min-height:100vh;background:#f4f7fb;color:#172033;padding-bottom:40px}
.student-top{display:flex;justify-content:space-between;align-items:center;padding:16px max(5%,calc((100% - 1180px)/2));background:#fff;border-bottom:1px solid #e5eaf0}
.student-top a{text-decoration:none}.student-top img{height:36px;vertical-align:middle;margin-right:9px}
.student-main{width:min(1180px,92%);margin:30px auto}
.welcome-card{background:linear-gradient(120deg,#122b49,#176b78);color:#fff;border-radius:18px;padding:26px;margin-bottom:20px}
.welcome-card h1{margin:7px 0}.welcome-card p{margin:5px 0;color:#e3edf4}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin:18px 0}
.info-box{background:#fff;border:1px solid #e4eaf0;border-radius:12px;padding:15px}.info-box small{display:block;color:#6b7788;margin-bottom:6px}.info-box strong{font-size:15px}
.section-card{background:#fff;border:1px solid #e4eaf0;border-radius:15px;padding:20px;margin-top:18px}
.section-head{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
.filters{display:flex;gap:10px;align-items:end;flex-wrap:wrap}.filters label{display:block;font-size:12px;color:#657386;margin-bottom:5px}.filters select{padding:10px 12px;border:1px solid #d5dde7;border-radius:8px;min-width:150px}
.btn-view{padding:10px 16px;background:#176b78;color:white;border:0;border-radius:8px;cursor:pointer}
.table-wrap{overflow:auto;margin-top:14px}table{width:100%;border-collapse:collapse;min-width:850px}th,td{padding:12px 10px;border-bottom:1px solid #edf0f4;text-align:left;font-size:13px}th{background:#f6f8fb;color:#536174;font-size:12px}
.pill{display:inline-block;padding:4px 8px;border-radius:20px;background:#e8f4f5;color:#176b78;font-size:11px;font-weight:700}
.notice{padding:14px;border-radius:10px;background:#fff8e8;color:#815b13;margin-top:12px;font-size:13px}
.empty{padding:22px;text-align:center;color:#758195}
@media(max-width:600px){.student-main{width:94%;margin:16px auto}.welcome-card{padding:20px}.section-card{padding:14px}}
</style>
</head>
<body class="student-page">
<header class="student-top">
<a href="student_dashboard.php"><img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt=""> <strong>STUDENT PORTAL</strong></a>
<a class="logout-btn" href="student_logout.php">Log out</a>
</header>
<main class="student-main">
<section class="welcome-card">
<span class="pill">STUDENT DASHBOARD</span>
<h1>Welcome, <?= e($student['name']) ?>!</h1>
<p>Your academic information, course list, marks and class routine in one place.</p>
</section>
<div class="info-grid">
<div class="info-box"><small>Student ID</small><strong><?= e($student['id']) ?></strong></div>
<div class="info-box"><small>Student Name</small><strong><?= e($student['name']) ?></strong></div>
<div class="info-box"><small>Department</small><strong><?= e($student['dept']) ?></strong></div>
<div class="info-box"><small>Phone</small><strong><?= e($student['phn_num']) ?></strong></div>
</div>

<section class="section-card">
<div class="section-head"><div><h2>My Courses & Academic Marks</h2><p>Semester-wise course and assessment information</p></div><span class="pill">4-month semester</span></div>
<?php if (!$academicDataReady): ?>
<div class="notice">Academic records are not set up yet. Admin must run <strong>student_academic_records_migration.sql</strong> first.</div>
<?php else: ?>
<form method="GET" class="filters">
<div><label>Semester</label><select name="semester"><option value="">All semesters</option><?php foreach(array_unique(array_column($semesters,'semester')) as $sem): ?><option value="<?= e($sem) ?>" <?= $selectedSemester===$sem?'selected':'' ?>><?= e($sem) ?></option><?php endforeach; ?></select></div>
<div><label>Academic Year</label><select name="year"><option value="">All years</option><?php foreach($years as $yr): ?><option value="<?= e($yr) ?>" <?= $selectedYear===$yr?'selected':'' ?>><?= e($yr) ?></option><?php endforeach; ?></select></div>
<button class="btn-view">Filter</button>
</form>
<?php if (!$records): ?><div class="empty">No course or mark records have been published for your account yet.</div>
<?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Course Code</th><th>Course Name</th><th>Semester</th><th>Academic Year</th><th>CT 1</th><th>CT 2</th><th>Midterm</th><th>Final</th><th>Total</th></tr></thead><tbody>
<?php foreach($records as $r): $vals=array_filter([$r['ct1_marks'],$r['ct2_marks'],$r['mid_marks'],$r['final_marks']],fn($v)=>$v!==null); $total=$vals?array_sum(array_map('floatval',$vals)):null; ?>
<tr><td><strong><?= e($r['course_code']) ?></strong></td><td><?= e($r['course_name']) ?></td><td><?= e($r['semester']) ?></td><td><?= e($r['academic_year']) ?></td><td><?= $r['ct1_marks']===null?'—':e($r['ct1_marks']) ?></td><td><?= $r['ct2_marks']===null?'—':e($r['ct2_marks']) ?></td><td><?= $r['mid_marks']===null?'—':e($r['mid_marks']) ?></td><td><?= $r['final_marks']===null?'—':e($r['final_marks']) ?></td><td><strong><?= $total===null?'—':e(number_format($total,2)) ?></strong></td></tr>
<?php endforeach; ?></tbody></table></div>
<p class="notice">Marks are displayed as entered by the department. Total is the sum of entered assessment marks; it is not a final grade calculation.</p>
<?php endif; ?>
<?php endif; ?>
</section>

<section class="section-card"><div class="section-head"><div><h2>BUBT Teacher Directory</h2><p>Find teachers and department heads by department.</p></div><a class="btn-view" href="student_teacher_directory.php">View Teacher Directory →</a></div></section>

<section class="section-card">
<div class="section-head"><div><h2>My Class Routine</h2><p>View your weekly class timetable.</p></div><a class="btn-view" href="view_routine.php">View Class Routine →</a></div>
</section>
</main>
</body></html>
