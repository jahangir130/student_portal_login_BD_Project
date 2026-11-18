<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if (!empty($_SESSION['teacher_id'])) {
    header('Location: teacher_dashboard.php');
    exit;
}

$error = '';
$success = '';
$data = ['name'=>'', 'department'=>'', 'designation'=>'', 'email'=>'', 'phone'=>'', 'address'=>''];
$departments = $pdo->query('SELECT department_name FROM bubt_departments ORDER BY department_name')->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($data as $key => $value) {
        $data[$key] = trim($_POST[$key] ?? '');
    }
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($data['name'] === '' || $data['department'] === '' || $data['designation'] === '' ||
        $data['email'] === '' || $data['phone'] === '' || $password === '' || $confirm === '') {
        $error = 'Please complete all required fields.';
    } elseif (strlen($data['name']) < 2 || strlen($data['name']) > 150) {
        $error = 'Please enter a valid full name (2–150 characters).';
    } elseif (!in_array($data['department'], $departments, true)) {
        $error = 'Please select a department from the official list.';
    } elseif (strlen($data['designation']) < 2 || strlen($data['designation']) > 120) {
        $error = 'Please enter a valid designation.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 150) {
        $error = 'Please enter a valid official email address.';
    } elseif (!preg_match('/^[0-9+()\-\s]{7,20}$/', $data['phone'])) {
        $error = 'Please enter a valid phone number.';
    } elseif (strlen($data['address']) > 255) {
        $error = 'Address must be 255 characters or fewer.';
    } elseif (strlen($password) < 8 || strlen($password) > 72) {
        $error = 'Password must contain 8–72 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirm password do not match.';
    } else {
        try {
            $st = $pdo->prepare("INSERT INTO teachers(name, department, designation, email, phone, address, password, account_status) VALUES(?,?,?,?,?,?,?,'pending')");
            $st->execute([
                $data['name'], $data['department'], $data['designation'], $data['email'],
                $data['phone'], $data['address'], password_hash($password, PASSWORD_DEFAULT)
            ]);
            $success = 'Registration submitted successfully. Your account is pending administrator approval. You can sign in after approval.';
            $data = ['name'=>'', 'department'=>'', 'designation'=>'', 'email'=>'', 'phone'=>'', 'address'=>''];
        } catch (PDOException $ex) {
            $error = $ex->getCode() === '23000'
                ? 'This email or phone number is already registered.'
                : 'Could not submit registration. Please check your database connection and teacher table setup.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher Registration | BUBT Student Portal</title>
    <link rel="stylesheet" href="assets/auth.css">
</head>

<body class="teacher-register-page">
    <div class="auth-shell">
        <aside class="auth-aside">
            <div class="aside-brand">
                <img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
                    alt="BUBT logo" onerror="this.style.display='none'">
                <span>BUBT STUDENT PORTAL</span>
            </div>
            <div class="aside-copy">
                <span class="eyebrow">TEACHER ACCOUNT</span>
                <h1>Join your campus portal.</h1>
                <p>Register with your official teaching information. An administrator will review your application
                    before account access is enabled.</p>
            </div>
            <div class="aside-bottom">A connected campus experience for teaching staff.</div>
        </aside>

        <main class="auth-main">
            <section class="auth-card">
                <div class="mobile-brand">
                    <img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
                        alt="BUBT logo" onerror="this.style.display='none'">
                    <span>BUBT STUDENT PORTAL</span>
                </div>
                <span class="eyebrow">TEACHER REGISTRATION</span>
                <h2>Create teacher account</h2>
                <p class="intro">Enter your official details to request a teacher account.</p>

                <?php if ($error): ?><div class="alert error" role="alert"><?= e($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert success" role="status"><?= e($success) ?></div><?php endif; ?>

                <form method="POST" action="teacher_register.php" autocomplete="on">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="form-grid">
                        <div class="field">
                            <label for="teacher-name">Full name *</label>
                            <input id="teacher-name" name="name" value="<?= e($data['name']) ?>"
                                placeholder="Enter your full name" maxlength="150" autocomplete="name" required>
                        </div>
                        <div class="field">
                            <label for="teacher-department">Department *</label>
                            <select id="teacher-department" name="department" required>
                                <option value="">Select your department</option>
                                <?php foreach ($departments as $department): ?>
                                <option value="<?= e($department) ?>"
                                    <?= $data['department'] === $department ? 'selected' : '' ?>><?= e($department) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="teacher-designation">Designation *</label>
                            <input id="teacher-designation" name="designation" value="<?= e($data['designation']) ?>"
                                placeholder="e.g. Lecturer, Assistant Professor" maxlength="120" required>
                        </div>
                        <div class="field">
                            <label for="teacher-email">Official email *</label>
                            <input id="teacher-email" type="email" name="email" value="<?= e($data['email']) ?>"
                                placeholder="name@university.edu" maxlength="150" autocomplete="email" required>
                        </div>
                        <div class="field">
                            <label for="teacher-phone">Phone number *</label>
                            <input id="teacher-phone" type="tel" name="phone" value="<?= e($data['phone']) ?>"
                                placeholder="01XXXXXXXXX" maxlength="20" autocomplete="tel" required>
                        </div>
                        <div class="field">
                            <label for="teacher-address">Address</label>
                            <input id="teacher-address" name="address" value="<?= e($data['address']) ?>"
                                placeholder="Your address (optional)" maxlength="255" autocomplete="street-address">
                        </div>
                        <div class="field">
                            <label for="teacher-password">Password *</label>
                            <input id="teacher-password" type="password" name="password" minlength="8" maxlength="72"
                                autocomplete="new-password" placeholder="At least 8 characters" required>
                            <small class="field-hint">Use 8–72 characters.</small>
                        </div>
                        <div class="field">
                            <label for="teacher-confirm-password">Confirm password *</label>
                            <input id="teacher-confirm-password" type="password" name="confirm_password" minlength="8"
                                maxlength="72" autocomplete="new-password" placeholder="Enter password again" required>
                        </div>
                    </div>
                    <button class="submit-btn" type="submit">Submit Registration <span>→</span></button>
                </form>

                <p class="approval-note">Your account remains pending until an administrator approves it.</p>
                <p class="switch-text">Already registered? <a href="teacher_login.php">Sign in here</a></p>
                <p class="admin-link"><a href="index.php">← Back to role selection</a> · <a
                        href="student_login.php">Student login</a> · <a href="admin_login.php">Admin login</a></p>
            </section>
        </main>
    </div>
    <script src="assets/password-toggle.js"></script>
</body>

</html>