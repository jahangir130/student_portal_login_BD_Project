<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin('admins');
$admins=$pdo->query('SELECT admin_id,name,email,phone,role,created_at FROM admins ORDER BY admin_id')->fetchAll();
page_start('Admin Panel');admin_header('admins');
?>
<main class="main-content">
    <header class="topbar">
        <div><span class="crumb">ACCESS MANAGEMENT</span>
            <h1>Admin Panel</h1>
        </div>
        <div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div>
    </header>
    <div class="page-intro">
        <div>
            <h2>Administrator accounts</h2>
            <p>View administrator details, help other admins sign in, and manage access.</p>
        </div><a class="btn primary" href="admin_form.php">＋ Add Admin</a>
    </div>
    <div class="panel" style="margin-bottom:18px">
        <div class="panel-head">
            <div>
                <h3>How another administrator can log in</h3>
                <p>Every admin uses the same login page with their own registered email and password.</p>
            </div>
        </div>
        <div style="padding:0 18px 18px">
            <p><strong>Login page:</strong> <code>http://localhost/student_admin_portal_v2/admin_login.php</code></p>
            <ol>
                <li>Share the login page address with the administrator.</li>
                <li>They enter the email address used when their account was created.</li>
                <li>They enter the password you assigned or securely shared with them.</li>
                <li>The system opens the dashboard according to their role.</li>
            </ol>
            <p class="hint">Passwords are stored as secure hashes. You cannot view an existing password here. If an
                admin forgets it, use Reset Password below and share the new password privately.</p>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head">
            <div>
                <h3>All Administrators <span class="count-pill"><?= count($admins) ?></span></h3>
                <p>Accounts with access to the admin portal</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Login Access</th>
                    </tr>
                </thead>
                <tbody><?php foreach($admins as $a): ?><tr>
                        <td>
                            <div class="person-cell"><span
                                    class="person-avatar"><?= e(strtoupper(substr($a['name'],0,1))) ?></span><strong><?= e($a['name']) ?></strong>
                            </div><small class="sub-id">Admin #<?= (int)$a['admin_id'] ?></small>
                        </td>
                        <td><?= e($a['email']) ?></td>
                        <td><?= e($a['phone']) ?></td>
                        <td><span class="tag"><?= e($a['role']) ?></span></td>
                        <td><?= e(date('d M Y',strtotime($a['created_at']))) ?></td>
                        <td><a class="action-edit" href="admin_password_reset.php?id=<?= (int)$a['admin_id'] ?>">Reset
                                Password</a></td>
                    </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
    <p class="hint">Only Super Admin can open this page and reset another administrator’s password. Never send passwords
        in public groups.</p><?php page_end(); ?>