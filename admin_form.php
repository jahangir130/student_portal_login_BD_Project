<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin('admins');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$phone=trim($_POST['phone']??'');$password=$_POST['password']??'';$role=trim($_POST['role']??'Admin'); if(!in_array($role,['Admin','Routine Manager','Super Admin'],true)){$role='Admin';}
 if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$phone===''||strlen($password)<8){$error='Enter valid details. Password must be at least 8 characters.';}
 else{try{$stmt=$pdo->prepare('INSERT INTO admins(name,email,phone,password,role) VALUES(?,?,?,?,?)');$stmt->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT),$role]);header('Location: admins.php');exit;}catch(PDOException $ex){$error=$ex->getCode()==='23000'?'Email or phone number already exists.':'Could not create admin account.';}}
}
page_start('Add Admin');admin_header('admins');
?>
<main class="main-content">
    <header class="topbar">
        <div><span class="crumb">ADMIN PANEL</span>
            <h1>Add Administrator</h1>
        </div>
        <div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div>
    </header>
    <div class="form-panel"><a class="back-link" href="admins.php">← Back to Admin Panel</a>
        <h2>New admin account</h2>
        <p class="muted">Create login credentials for another administrator. Only a Super Admin can create administrator
            accounts.</p><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-grid">
                <div><label>Full name *</label><input name="name" required></div>
                <div><label>Email *</label><input type="email" name="email" required></div>
                <div><label>Phone *</label><input name="phone" required></div>
                <div><label>Password (minimum 8 characters) *</label><input type="password" name="password"
                        minlength="8" required autocomplete="new-password"></div>
                <div><label>Admin role *</label><select name="role">
                        <option value="Admin">Admin — student, teacher and routine management</option>
                        <option value="Routine Manager">Routine Manager — routine management only</option>
                        <option value="Super Admin">Super Admin — full access and admin accounts</option>
                    </select></div>
            </div>
            <div class="form-actions"><a class="btn light" href="admins.php">Cancel</a><button
                    class="btn primary">Create Admin</button></div>
        </form>
    </div><?php page_end(); ?>