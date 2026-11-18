<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin('teachers');
$search=trim($_GET['search']??'');
$statusFilter=trim($_GET['status']??'');
$where=[]; $params=[];
if($statusFilter==='pending'){$where[]="account_status='pending'";}
if($statusFilter==='approved'){$where[]="account_status='approved'";}
if($search!==''){$like='%'.$search.'%';$where[]='(name LIKE ? OR department LIKE ? OR email LIKE ? OR phone LIKE ?)';array_push($params,$like,$like,$like,$like);}
$sql='SELECT * FROM teachers'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY teacher_id DESC';
$st=$pdo->prepare($sql);$st->execute($params);$teachers=$st->fetchAll();
page_start('Teachers Section');admin_header('teachers'); ?>
<main class="main-content">
    <header class="topbar">
        <div><span class="crumb">MANAGEMENT</span>
            <h1>Teachers Section</h1>
        </div>
        <div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div>
    </header>
    <div class="page-intro">
        <div>
            <h2><?= $statusFilter==='pending'?'Pending teacher approvals':'Teacher directory' ?></h2>
            <p><?= $statusFilter==='pending'?'Review and approve newly registered teacher accounts.':'Review registrations and manage teacher information.' ?>
            </p>
        </div><a class="btn primary" href="teacher_form.php">＋ Add New Teacher</a>
    </div>
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3><?= $statusFilter==='pending'?'Pending Teachers':'All Teachers' ?> <span
                        class="count-pill"><?= count($teachers) ?></span></h3>
                <p>Pending registrations must be approved before login.</p>
            </div>
            <div class="teacher-filter-links"><a class="<?= $statusFilter==='pending'?'active':'' ?>"
                    href="teachers.php?status=pending">Pending</a><a
                    class="<?= $statusFilter==='approved'?'active':'' ?>"
                    href="teachers.php?status=approved">Approved</a><a href="teachers.php">All</a></div>
            <form class="search-form" method="GET"><?php if($statusFilter!==''): ?><input type="hidden" name="status"
                    value="<?= e($statusFilter) ?>"><?php endif; ?><input name="search" value="<?= e($search) ?>"
                    placeholder="Search teacher..."><button class="btn dark">Search</button></form>
        </div>
        <?php if(!$teachers): ?><p class="empty">No teachers found.</p><?php else: ?><div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($teachers as $t): $status=$t['account_status']??'approved'; ?><tr>
                        <td>
                            <div class="person-cell"><span
                                    class="person-avatar"><?= e(strtoupper(substr($t['name'],0,1))) ?></span><strong><?= e($t['name']) ?></strong>
                            </div><small class="sub-id">Teacher ID: <?= (int)$t['teacher_id'] ?></small>
                        </td>
                        <td><?= e($t['department']) ?></td>
                        <td><span class="tag"><?= e($t['designation']) ?></span></td>
                        <td><?= e($t['email']) ?></td>
                        <td><?= e($t['phone']) ?></td>
                        <td><?= $status==='pending'?'<span class="tag">Pending</span>':'<span class="tag">Approved</span>' ?>
                        </td>
                        <td>
                            <div class="actions"><?php if($status==='pending'): ?><form method="POST"
                                    action="teacher_approve.php"><input type="hidden" name="csrf_token"
                                        value="<?= e(csrf_token()) ?>"><input type="hidden" name="id"
                                        value="<?= (int)$t['teacher_id'] ?>"><button
                                        class="action-edit">Approve</button></form><?php endif; ?><a class="action-edit"
                                    href="teacher_form.php?id=<?= (int)$t['teacher_id'] ?>">Edit</a>
                                <form method="POST" action="teacher_delete.php"
                                    onsubmit="return confirm('Delete this teacher?')"><input type="hidden"
                                        name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id"
                                        value="<?= (int)$t['teacher_id'] ?>"><button
                                        class="action-delete">Delete</button></form>
                            </div>
                        </td>
                    </tr><?php endforeach; ?></tbody>
            </table>
        </div><?php endif; ?>
    </div><?php page_end(); ?>