<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin('teachers');
$department=trim($_GET['department']??''); $search=trim($_GET['search']??'');
$departments=$pdo->query('SELECT department_name FROM bubt_departments ORDER BY department_name')->fetchAll(PDO::FETCH_COLUMN);
$where=[]; $args=[];
if($department!==''){$where[]='department_name=?';$args[]=$department;}
if($search!==''){$where[]='(teacher_name LIKE ? OR designation LIKE ? OR department_name LIKE ?)';$like='%'.$search.'%';array_push($args,$like,$like,$like);}
$sql='SELECT teacher_name,designation,department_name,directory_role,verified_on FROM bubt_teacher_directory'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY department_name, directory_role, teacher_name';
$q=$pdo->prepare($sql);$q->execute($args);$rows=$q->fetchAll();
$total=(int)$pdo->query('SELECT COUNT(*) FROM bubt_teacher_directory')->fetchColumn();
page_start('BUBT Directory'); admin_header('directory'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">UNIVERSITY DATA</span><h1>BUBT Directory</h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="page-intro"><div><h2>Departments & Faculty Directory</h2><p>Imported directory records supplied for the portal. These are public directory entries, not teacher login accounts.</p></div><div class="intro-actions"><a class="btn light" href="admin_departments.php">View Departments</a><a class="btn primary" href="student_teacher_directory.php">Open Public Directory</a></div></div>
<section class="stats-grid" style="grid-template-columns:repeat(2,minmax(0,1fr))"><div class="stat-card"><span class="stat-icon blue">DP</span><p>BUBT Departments</p><strong><?= count($departments) ?></strong><small>Departments in supplied data</small></div><div class="stat-card"><span class="stat-icon violet">FC</span><p>Directory Entries</p><strong><?= number_format($total) ?></strong><small>Faculty and department heads</small></div></section>
<section class="panel"><div class="panel-head"><div><h3>Teacher Directory <span class="count-pill"><?= count($rows) ?> shown</span></h3><p>Filter by department or search names/designations</p></div></div>
<form class="search-form" method="get" style="flex-wrap:wrap;margin-bottom:16px"><input name="search" value="<?= e($search) ?>" placeholder="Search teacher or designation"><select name="department" style="min-width:220px"><option value="">All departments</option><?php foreach($departments as $d): ?><option value="<?= e($d) ?>" <?= $department===$d?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?></select><button class="btn primary">Apply Filter</button><a class="btn light" href="admin_bubt_directory.php">Reset</a></form>
<?php if(!$rows): ?><p class="empty">No directory entries found. Run bubt_directory_data_import.sql first.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Teacher Name</th><th>Designation</th><th>Department</th><th>Directory Role</th><th>Verified On</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['teacher_name']) ?></td><td><?= e($r['designation']??'—') ?></td><td><?= e($r['department_name']) ?></td><td><span class="tag"><?= e($r['directory_role']) ?></span></td><td><?= e($r['verified_on']??'—') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<p class="copyright">Directory data is kept separate from teacher authentication and contact information.</p></main><?php page_end(); ?>
