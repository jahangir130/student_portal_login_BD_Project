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
function require_admin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }
}
function admin_header($active = 'dashboard') {
    $items = [
        'dashboard' => ['Dashboard', 'admin_dashboard.php'],
        'admins' => ['Admin Panel', 'admins.php'],
        'teachers' => ['Teachers Section', 'teachers.php'],
        'students' => ['Students', 'students.php'],
        'routines' => ['Class Routine', 'routines.php']
    ];
    echo '<aside class="sidebar"><a class="side-brand" href="admin_dashboard.php"><span class="brand-icon">SP</span><span>Student Portal<small>ADMINISTRATION</small></span></a><nav>';
    foreach ($items as $key => $item) {
        $class = $active === $key ? 'nav-link active' : 'nav-link';
        echo '<a class="'.$class.'" href="'.$item[1].'"><span class="nav-dot"></span>'.e($item[0]).'</a>';
    }
    echo '</nav><div class="side-bottom"><div class="admin-mini"><span class="mini-avatar">A</span><span>'.e($_SESSION['admin_name'] ?? 'Administrator').'<small>Administrator</small></span></div><a class="logout-side" href="admin_logout.php">↪ Log out</a></div></aside>';
}
function page_start($title) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.e($title).' | Student Portal</title><link rel="stylesheet" href="assets/admin.css"></head><body class="admin-body"><div class="admin-shell">';
}
function page_end() {
    echo '</div></body></html>';
}
