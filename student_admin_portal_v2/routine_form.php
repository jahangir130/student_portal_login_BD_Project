<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin();
$id=(int)($_GET['id']??0);$editing=$id>0;$error='';
$data=['department'=>'','semester'=>'','class_day'=>'Monday','course_code'=>'','course_name'=>'','teacher_name'=>'','start_time'=>'09:00','end_time'=>'10:00','room_no'=>''];
if($editing){$st=$pdo->prepare('SELECT * FROM class_routines WHERE routine_id=?');$st->execute([$id]);$found=$st->fetch();if(!$found){http_response_code(404);exit('Routine entry not found.');}$data=$found;}
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();foreach($data as $k=>$v){$data[$k]=trim($_POST[$k]??'');}
$days=['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
if($data['department']===''||$data['semester']===''||!in_array($data['class_day'],$days,true)||$data['course_code']===''||$data['course_name']===''||$data['teacher_name']===''||$data['start_time']===''||$data['end_time']===''||$data['room_no']===''){$error='Please fill in all required fields.';}
elseif(strtotime($data['end_time'])<=strtotime($data['start_time'])){$error='End time must be later than start time.';}
else{if($editing){$st=$pdo->prepare('UPDATE class_routines SET department=?,semester=?,class_day=?,course_code=?,course_name=?,teacher_name=?,start_time=?,end_time=?,room_no=? WHERE routine_id=?');$st->execute([$data['department'],$data['semester'],$data['class_day'],$data['course_code'],$data['course_name'],$data['teacher_name'],$data['start_time'],$data['end_time'],$data['room_no'],$id]);}
else{$st=$pdo->prepare('INSERT INTO class_routines(department,semester,class_day,course_code,course_name,teacher_name,start_time,end_time,room_no,created_by) VALUES(?,?,?,?,?,?,?,?,?,?)');$st->execute([$data['department'],$data['semester'],$data['class_day'],$data['course_code'],$data['course_name'],$data['teacher_name'],$data['start_time'],$data['end_time'],$data['room_no'],$_SESSION['admin_id']]);}header('Location: routines.php');exit;}}
page_start($editing?'Edit Class':'Add Class');admin_header('routines'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">ACADEMIC MANAGEMENT</span><h1><?= $editing?'Edit Class':'Add Class to Routine' ?></h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="form-panel"><a class="back-link" href="routines.php">← Back to Class Routine</a><h2>Class details</h2><p class="muted">Enter the schedule information below.</p><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<div><label>Department *</label><input name="department" value="<?= e($data['department']) ?>" placeholder="CSE" required></div>
<div><label>Semester / Intake *</label><input name="semester" value="<?= e($data['semester']) ?>" placeholder="e.g. 55th Intake, Section 08" required></div>
<div><label>Day *</label><select name="class_day"><?php foreach(['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'] as $d): ?><option <?= $data['class_day']===$d?'selected':'' ?>><?= $d ?></option><?php endforeach; ?></select></div>
<div><label>Course Code *</label><input name="course_code" value="<?= e($data['course_code']) ?>" placeholder="CSE 207" required></div>
<div><label>Course Name *</label><input name="course_name" value="<?= e($data['course_name']) ?>" required></div>
<div><label>Teacher Name *</label><input name="teacher_name" value="<?= e($data['teacher_name']) ?>" required></div>
<div><label>Start Time *</label><input type="time" name="start_time" value="<?= e(substr($data['start_time'],0,5)) ?>" required></div>
<div><label>End Time *</label><input type="time" name="end_time" value="<?= e(substr($data['end_time'],0,5)) ?>" required></div>
<div><label>Room Number *</label><input name="room_no" value="<?= e($data['room_no']) ?>" placeholder="Room 501" required></div>
</div><div class="form-actions"><a class="btn light" href="routines.php">Cancel</a><button class="btn primary">Save Class</button></div></form></div><?php page_end(); ?>
