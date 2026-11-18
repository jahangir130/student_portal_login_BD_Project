<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
require_admin('admins');

$id = (int)($_GET['id'] ?? $_POST['admin_id'] ?? 0);
$stmt = $pdo->prepare('SELECT admin_id,name,email,role FROM admins WHERE admin_id=? LIMIT 1');
$stmt->execute([$id]);
$target = $stmt->fetch();
if (!$target) { http_response_code(404); exit('Administrator account not found.'); }

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password and confirm password do not match.';
    } else {
        $update = $pdo->prepare('UPDATE admins SET password=? WHERE admin_id=?');
        $update->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
        $success = true;
    }
}
page_start('Reset Admin Password'); admin_header('admins');
?>
<main class="main-content">
    <header class="topbar">
        <div><span class="crumb">ACCESS MANAGEMENT</span>
            <h1>Reset Admin Password</h1>
        </div>
        <div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div>
    </header>
    <div class="form-panel">
        <a class="back-link" href="admins.php">← Back to Admin Panel</a>
        <h2><?= e($target['name']) ?></h2>
        <p class="muted"><?= e($target['email']) ?> · <?= e($target['role']) ?></p>
        <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?>
        <div class="success">Password updated successfully. Share the new password with this administrator privately.
            They can now sign in at <strong>admin_login.php</strong>.</div>
        <a class="btn primary" href="admins.php">Return to Admin Panel</a>
        <?php else: ?>
        <p class="hint">Existing passwords cannot be viewed. Set a new password only after confirming the account
            owner’s identity.</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="admin_id" value="<?= (int)$target['admin_id'] ?>">
            <div class="form-grid">
                <div><label>New password (minimum 8 characters) *</label><input type="password" name="password"
                        minlength="8" required autocomplete="new-password"></div>
                <div><label>Confirm new password *</label><input type="password" name="confirm_password" minlength="8"
                        required autocomplete="new-password"></div>
            </div>
            <div class="form-actions"><a class="btn light" href="admins.php">Cancel</a><button class="btn primary"
                    type="submit">Update Password</button></div>
        </form>
        <?php endif; ?>
    </div>
</main>
<?php page_end(); ?>