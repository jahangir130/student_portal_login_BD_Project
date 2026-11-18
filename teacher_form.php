<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin();
$id=(int)($_GET['id']??0);$editing=$id>0;$error='';
$data=['name'=>'','department'=>'','designation'=>'','email'=>'','phone'=>'','address'=>''];
if($editing){$st=$pdo->prepare('SELECT * FROM teachers WHERE teacher_id=?');$st->execute([$id]);$found=$st->fetch();if(!$found){http_response_code(404);exit('Teacher not found.');}$data=$found;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();foreach($data as $key=>$value){$data[$key]=trim($_POST[$key]??'');}
 $password=$_POST['password']??'';
 if($data['name']===''||$data['department']===''||$data['designation']===''||!filter_var($data['email'],FILTER_VALIDATE_EMAIL)||$data['phone']===''){$error='Please complete all required fields with a valid email.';}
 elseif(!$editing&&strlen($password)<8){$error='Set a teacher login password with at least 8 characters.';}
 elseif($editing&&$password!==''&&strlen($password)<8){$error='New password must contain at least 8 characters.';}
 else{try{
  if($editing){
   if($password!==''){$st=$pdo->prepare('UPDATE teachers SET name=?,department=?,designation=?,email=?,phone=?,address=?,password=? WHERE teacher_id=?');$st->execute([$data['name'],$data['department'],$data['designation'],$data['email'],$data['phone'],$data['address'],password_hash($password,PASSWORD_DEFAULT),$id]);}
   else{$st=$pdo->prepare('UPDATE teachers SET name=?,department=?,designation=?,email=?,phone=?,address=? WHERE teacher_id=?');$st->execute([$data['name'],$data['department'],$data['designation'],$data['email'],$data['phone'],$data['address'],$id]);}
  }else{$st=$pdo->prepare('INSERT INTO teachers(name,department,designation,email,phone,address,password,account_status) VALUES(?,?,?,?,?,?,?,'approved')');$st->execute([$data['name'],$data['department'],$data['designation'],$data['email'],$data['phone'],$data['address'],password_hash($password,PASSWORD_DEFAULT)]);}
  header('Location: teachers.php');exit;
 }catch(PDOException $ex){$error=$ex->getCode()==='23000'?'Email or phone number is already registered.':'Unable to save teacher details. Make sure teacher_auth_migration.sql has been run.';}}
}
page_start($editing?'Edit Teacher':'Add Teacher');admin_header('teachers'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">TEACHER MANAGEMENT</span><h1><?= $editing?'Edit Teacher':'Add New Teacher' ?></h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="form-panel"><a class="back-link" href="teachers.php">← Back to Teachers</a><h2><?= $editing?'Update teacher information':'Teacher information and login' ?></h2><p class="muted">Fields marked * are required. Set a password so the teacher can sign in.</p><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<div><label>Teacher name *</label><input name="name" value="<?= e($data['name']) ?>" required></div>
<div><label>Department *</label><input name="department" value="<?= e($data['department']) ?>" placeholder="e.g. CSE" required></div>
<div><label>Designation *</label><input name="designation" value="<?= e($data['designation']) ?>" placeholder="e.g. Lecturer" required></div>
<div><label>Email * (login email)</label><input type="email" name="email" value="<?= e($data['email']) ?>" required></div>
<div><label>Phone number *</label><input name="phone" value="<?= e($data['phone']) ?>" required></div>
<div><label>Address</label><input name="address" value="<?= e($data['address']) ?>"></div>
<div><label><?= $editing?'New password (leave blank to keep current)':'Teacher login password *' ?></label><input type="password" name="password" minlength="8" <?= $editing?'':'required' ?> placeholder="At least 8 characters"></div>
</div><div class="form-actions"><a class="btn light" href="teachers.php">Cancel</a><button class="btn primary">Save Teacher</button></div></form></div><?php page_end(); ?>
