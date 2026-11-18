<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(!empty($_SESSION['teacher_id'])){header('Location: teacher_dashboard.php');exit;}
$error='';$success='';$data=['name'=>'','department'=>'','designation'=>'','email'=>'','phone'=>'','address'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();foreach($data as $key=>$value){$data[$key]=trim($_POST[$key]??'');}
 $password=$_POST['password']??'';$confirm=$_POST['confirm_password']??'';
 if($data['name']===''||$data['department']===''||$data['designation']===''||!filter_var($data['email'],FILTER_VALIDATE_EMAIL)||$data['phone']===''){$error='Please complete all required fields and enter a valid email.';}
 elseif(strlen($password)<8){$error='Password must contain at least 8 characters.';}
 elseif($password!==$confirm){$error='Password and confirm password do not match.';}
 else{try{$st=$pdo->prepare("INSERT INTO teachers(name,department,designation,email,phone,address,password,account_status) VALUES(?,?,?,?,?,?,?,'pending')");
 $st->execute([$data['name'],$data['department'],$data['designation'],$data['email'],$data['phone'],$data['address'],password_hash($password,PASSWORD_DEFAULT)]);
 $success='Registration submitted. Your account is pending administrator approval. You can sign in after approval.';
 $data=['name'=>'','department'=>'','designation'=>'','email'=>'','phone'=>'','address'=>''];
 }catch(PDOException $ex){$error=$ex->getCode()==='23000'?'This email or phone number is already registered.':'Could not submit registration. Apply teacher registration database update first.';}}
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Teacher Registration | Student Portal</title><link rel="stylesheet" href="assets/auth.css"></head><body>
<div class="auth-shell"><aside class="auth-aside"><div class="aside-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div><div class="aside-copy"><span class="eyebrow">TEACHER ACCOUNT</span><h1>Join your campus portal.</h1><p>Register your teacher profile. Your administrator will review and approve your account before sign-in is enabled.</p></div><div class="aside-bottom">Verified access for teaching staff.</div></aside>
<main class="auth-main"><section class="auth-card"><div class="mobile-brand"><img src="assets/portal-logo.jpg" alt="Portal logo"><span>STUDENT PORTAL</span></div><span class="eyebrow">TEACHER REGISTRATION</span><h2>Create teacher account</h2><p class="intro">Submit your official details for administrator approval.</p>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<div class="field"><label>Full name *</label><input name="name" value="<?= e($data['name']) ?>" required></div>
<div class="field"><label>Department *</label><input name="department" value="<?= e($data['department']) ?>" placeholder="e.g. CSE" required></div>
<div class="field"><label>Designation *</label><input name="designation" value="<?= e($data['designation']) ?>" placeholder="e.g. Lecturer" required></div>
<div class="field"><label>Official email *</label><input type="email" name="email" value="<?= e($data['email']) ?>" required></div>
<div class="field"><label>Phone number *</label><input name="phone" value="<?= e($data['phone']) ?>" required></div>
<div class="field"><label>Address</label><input name="address" value="<?= e($data['address']) ?>"></div>
<div class="field"><label>Password *</label><input type="password" name="password" minlength="8" placeholder="At least 8 characters" required></div>
<div class="field"><label>Confirm password *</label><input type="password" name="confirm_password" minlength="8" placeholder="Type password again" required></div>
</div><button class="submit-btn" type="submit">Submit Registration <span>→</span></button></form>
<p class="switch-text">Already registered? <a href="teacher_login.php">Sign in here</a></p>
<p class="admin-link"><a href="index.php">← Back to role selection</a> · <a href="student_login.php">Student login</a> · <a href="admin_login.php">Admin login</a></p>
</section></main></div></body></html>
