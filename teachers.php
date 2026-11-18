<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin();
$search=trim($_GET['search']??'');
if($search!==''){$like='%'.$search.'%';$st=$pdo->prepare('SELECT * FROM teachers WHERE name LIKE ? OR department LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY teacher_id DESC');$st->execute([$like,$like,$like,$like]);$teachers=$st->fetchAll();}
else{$teachers=$pdo->query('SELECT * FROM teachers ORDER BY teacher_id DESC')->fetchAll();}
page_start('Teachers Section');admin_header('teachers'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">MANAGEMENT</span><h1>Teachers Section</h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="page-intro"><div><h2>Teacher directory</h2><p>Review registrations and manage teacher information.</p></div><a class="btn primary" href="teacher_form.php">＋ Add New Teacher</a></div>
<div class="panel"><div class="panel-head"><div><h3>All Teachers <span class="count-pill"><?= count($teachers) ?></span></h3><p>Pending registrations must be approved before login.</p></div><form class="search-form" method="GET"><input name="search" value="<?= e($search) ?>" placeholder="Search teacher..."><button class="btn dark">Search</button></form></div>
<?php if(!$teachers): ?><p class="empty">No teachers found.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Teacher</th><th>Department</th><th>Designation</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($teachers as $t): $status=$t['account_status']??'approved'; ?><tr>
<td><div class="person-cell"><span class="person-avatar"><?= e(strtoupper(substr($t['name'],0,1))) ?></span><strong><?= e($t['name']) ?></strong></div><small class="sub-id">Teacher ID: <?= (int)$t['teacher_id'] ?></small></td>
<td><?= e($t['department']) ?></td><td><span class="tag"><?= e($t['designation']) ?></span></td><td><?= e($t['email']) ?></td><td><?= e($t['phone']) ?></td>
<td><?= $status==='pending'?'<span class="tag">Pending</span>':'<span class="tag">Approved</span>' ?></td>
<td><div class="actions"><?php if($status==='pending'): ?><form method="POST" action="teacher_approve.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$t['teacher_id'] ?>"><button class="action-edit">Approve</button></form><?php endif; ?><a class="action-edit" href="teacher_form.php?id=<?= (int)$t['teacher_id'] ?>">Edit</a><form method="POST" action="teacher_delete.php" onsubmit="return confirm('Delete this teacher?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$t['teacher_id'] ?>"><button class="action-delete">Delete</button></form></div></td>
</tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div><?php page_end(); ?>
