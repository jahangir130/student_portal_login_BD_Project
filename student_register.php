<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
if (!empty($_SESSION['student_id'])) { header('Location: student_dashboard.php'); exit; }

$error = ''; $success = '';
$old = ['id'=>'','name'=>'','dept'=>'','phn_num'=>''];
$departments = $pdo->query('SELECT department_name FROM bubt_departments ORDER BY department_name')->fetchAll(PDO::FETCH_COLUMN);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($old as $k=>$v) $old[$k] = trim($_POST[$k] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (in_array('', $old, true) || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!preg_match('/^[0-9A-Za-z_-]{3,30}$/', $old['id'])) {
        $error = 'Student ID must be 3–30 characters (letters, numbers, _ or -).';
    } elseif (strlen($old['name']) < 2 || strlen($old['name']) > 100) {
        $error = 'Please enter a valid full name.';
    } elseif (!in_array($old['dept'], $departments, true)) {
        $error = 'Please select a department from the list.';
    } elseif (!preg_match('/^[0-9+()\-\s]{7,20}$/', $old['phn_num'])) {
        $error = 'Please enter a valid phone number.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirm password do not match.';
    } else {
        try {
            $pdo->beginTransaction();
            $st = $pdo->prepare('INSERT INTO student(id,name,dept) VALUES(?,?,?)');
            $st->execute([$old['id'],$old['name'],$old['dept']]);
            $st = $pdo->prepare('INSERT INTO `user`(id,phn_num,password) VALUES(?,?,?)');
            $st->execute([$old['id'],$old['phn_num'],password_hash($password,PASSWORD_DEFAULT)]);
            $pdo->commit();
            $success = 'Registration successful. You can now sign in.';
            $old = ['id'=>'','name'=>'','dept'=>'','phn_num'=>''];
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $ex->getCode() === '23000'
                ? 'This Student ID or phone number is already registered.'
                : 'Registration failed. Please check your database connection.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration | Student Portal</title>
    <link rel="stylesheet" href="assets/auth.css">
</head>

<body class="register-page">
    <div class="auth-shell">
        <aside class="auth-aside">
            <div class="aside-brand"><img
                    src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
                    alt="BUBT logo" onerror="this.style.display='none'"><span>BUBT STUDENT PORTAL</span></div>
            <div class="aside-copy"><span class="eyebrow">WELCOME TO YOUR CAMPUS</span>
                <h1>Start your academic journey.</h1>
                <p>Create your student account and access your academic information from one place.</p>
            </div>
            <div class="aside-bottom">Simple access. Better campus experience.</div>
        </aside>
        <main class="auth-main">
            <section class="auth-card">
                <div class="mobile-brand"><img
                        src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
                        alt="BUBT logo" onerror="this.style.display='none'"><span>BUBT STUDENT PORTAL</span></div>
                <span class="eyebrow">STUDENT ACCOUNT</span>
                <h2>Create your account</h2>
                <p class="intro">Enter your details to register as a student.</p>
                <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
                <form method="POST" action="student_register.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="form-grid">
                        <div class="field"><label for="student-id">Student ID</label><input id="student-id" name="id"
                                value="<?= e($old['id']) ?>" placeholder="e.g. 20255100000" minlength="3" maxlength="30"
                                autocomplete="username" required></div>
                        <div class="field"><label for="student-name">Full name</label><input id="student-name"
                                name="name" value="<?= e($old['name']) ?>" placeholder="Enter your full name"
                                maxlength="100" autocomplete="name" required></div>
                        <div class="field"><label for="department">Department</label><select id="department" name="dept"
                                required>
                                <option value="">Select your department</option>
                                <?php foreach($departments as $department): ?><option value="<?= e($department) ?>"
                                    <?= $old['dept']===$department?'selected':'' ?>><?= e($department) ?></option>
                                <?php endforeach; ?>
                            </select></div>
                        <div class="field"><label for="phone">Phone number</label><input id="phone" name="phn_num"
                                type="tel" value="<?= e($old['phn_num']) ?>" placeholder="01XXXXXXXXX" maxlength="20"
                                autocomplete="tel" required></div>
                        <div class="field"><label for="password">Password</label><input id="password" name="password"
                                type="password" minlength="8" maxlength="72" autocomplete="new-password"
                                placeholder="At least 8 characters" required><small class="field-hint">Use at least 8
                                characters.</small></div>
                        <div class="field"><label for="confirm-password">Confirm password</label><input
                                id="confirm-password" name="confirm_password" type="password" minlength="8"
                                maxlength="72" autocomplete="new-password" placeholder="Re-enter your password"
                                required></div>
                    </div>
                    <button class="submit-btn" type="submit">Create Account <span>→</span></button>
                </form>
                <p class="switch-text">Already have an account? <a href="student_login.php">Sign in here</a></p>
                <p class="admin-link">Administrator? <a href="admin_login.php">Admin login</a> <span
                        aria-hidden="true">·</span> <a href="index.php">Back to portal</a></p>
            </section>
        </main>
    </div>
    <script src="assets/password-toggle.js"></script>
</body>

</html>