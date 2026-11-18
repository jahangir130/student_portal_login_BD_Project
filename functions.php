<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf() {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(419);
        exit('Invalid form token. Please go back and try again.');
    }
}
function current_admin_role() {
    return $_SESSION['admin_role'] ?? 'Admin';
}
function require_admin($area = null) {
    if (empty($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }
    $role = current_admin_role();
    $allowed = [
        'Super Admin' => ['dashboard','students','teachers','routines','admins'],
        'Admin' => ['dashboard','students','teachers','routines'],
        'Routine Manager' => ['dashboard','routines']
    ];
    if (!isset($allowed[$role])) {
        http_response_code(403);
        exit('Access denied: your administrator role is not recognized.');
    }
    if ($area !== null && !in_array($area, $allowed[$role], true)) {
        http_response_code(403);
        exit('Access denied: your role does not have permission to open this section.');
    }
}
function admin_header($active = 'dashboard') {
    $role = current_admin_role();
    $items = ['dashboard' => ['Dashboard', 'admin_dashboard.php']];
    if ($role === 'Super Admin') $items['admins'] = ['Admin Panel', 'admins.php'];
    if ($role !== 'Routine Manager') {
        $items['teachers'] = ['Teachers Section', 'teachers.php'];
        $items['directory'] = ['BUBT Directory', 'admin_bubt_directory.php'];
        $items['students'] = ['Students', 'students.php'];
    }
    $items['routines'] = ['Class Routine', 'routines.php'];
    echo '<aside class="sidebar"><a class="side-brand" href="admin_dashboard.php"><span class="brand-icon">SP</span><span>Student Portal<small>ADMINISTRATION</small></span></a><nav>';
    foreach ($items as $key => $item) {
        $class = $active === $key ? 'nav-link active' : 'nav-link';
        echo '<a class="'.$class.'" href="'.$item[1].'"><span class="nav-dot"></span>'.e($item[0]).'</a>';
    }
    echo '</nav><div class="side-bottom"><div class="admin-mini"><span class="mini-avatar">A</span><span>'.e($_SESSION['admin_name'] ?? 'Administrator').'<small>'.e(current_admin_role()).'</small></span></div><a class="logout-side" href="admin_logout.php">↪ Log out</a></div></aside>';
}
function page_start($title) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.e($title).' | Student Portal</title><link rel="stylesheet" href="assets/admin.css"><script defer src="assets/password-toggle.js"></script><style>.bubt-global-brand{display:flex;align-items:center;gap:10px;padding:10px 22px;background:#fff;border-bottom:1px solid #e5e7eb;font:700 13px Arial,sans-serif;color:#183b75;text-decoration:none;position:relative;z-index:5}.bubt-global-brand img{width:42px;height:42px;object-fit:contain}.bubt-global-brand span{letter-spacing:.4px}</style></head><body class="admin-body"><a class="bubt-global-brand" href="index.php"><img src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt="BUBT Logo"><span>Bangladesh University of Business &amp; Technology</span></a><div class="admin-shell">';
}
function page_end() {
    echo '</div></body></html>';
}
