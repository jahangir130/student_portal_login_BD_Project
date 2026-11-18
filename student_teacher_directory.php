<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
if (empty($_SESSION['student_id'])) { header('Location: student_login.php'); exit; }
$dept = trim($_GET['department'] ?? '');
$departments = $pdo->query("SELECT department_name FROM bubt_departments ORDER BY department_name")->fetchAll(PDO::FETCH_COLUMN);
$sql = "SELECT teacher_name, designation, department_name, directory_role, source_url, verified_on FROM bubt_teacher_directory";
$args = [];
if ($dept !== '') { $sql .= " WHERE department_name=?"; $args[]=$dept; }
$sql .= " ORDER BY department_name, teacher_name";
$q=$pdo->prepare($sql); $q->execute($args); $teachers=$q->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BUBT Teacher Directory</title>
    <link rel="stylesheet" href="assets/admin.css">
    <style>
    body {
        background: #f4f7fb
    }

    .directory {
        max-width: 1100px;
        margin: 32px auto;
        padding: 0 18px
    }

    .head {
        background: #12324b;
        color: #fff;
        border-radius: 16px;
        padding: 24px
    }

    .head p {
        color: #dce8ef
    }

    .filter,
    .list {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        margin-top: 16px
    }

    .filter form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: end
    }

    .filter select {
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        min-width: 250px
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 12px;
        margin-top: 14px
    }

    .teacher {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px
    }

    .teacher h3 {
        margin: 0 0 6px
    }

    .muted {
        color: #64748b;
        font-size: 13px
    }

    .tag {
        display: inline-block;
        background: #e7f3f4;
        color: #155e63;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        margin-bottom: 8px
    }

    .notice {
        font-size: 12px;
        color: #64748b;
        margin-top: 16px
    }
    </style>
    <style>
    .bubt-global-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 22px;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        font: 700 13px Arial, sans-serif;
        color: #183b75;
        text-decoration: none;
        position: relative;
        z-index: 5
    }

    .bubt-global-brand img {
        width: 42px;
        height: 42px;
        object-fit: contain
    }

    .bubt-global-brand span {
        letter-spacing: .4px
    }
    </style>
</head>

<body><a class="bubt-global-brand" href="index.php"><img
            src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
            alt="BUBT Logo"><span>Bangladesh University of Business &amp; Technology</span></a>
    <main class="directory">
        <div class="head"><a href="student_dashboard.php" style="color:#fff">← Back to Dashboard</a>
            <h1>BUBT Teacher Directory</h1>
            <p>Publicly listed departmental leadership and faculty information.</p>
        </div>
        <section class="filter">
            <form method="get">
                <div><label for="department">Department</label><br><select name="department" id="department">
                        <option value="">All Departments</option><?php foreach($departments as $d): ?><option
                            value="<?= e($d) ?>" <?= $dept===$d?'selected':'' ?>><?= e($d) ?></option>
                        <?php endforeach; ?>
                    </select></div><button class="btn primary">Filter</button>
            </form>
        </section>
        <section class="list">
            <h2>Teachers & Department Heads (<?= count($teachers) ?>)</h2><?php if(!$teachers): ?><p>No directory
                records found. Run the directory migration first.</p><?php else: ?><div class="grid">
                <?php foreach($teachers as $t): ?><article class="teacher"><span
                        class="tag"><?= e($t['directory_role']) ?></span>
                    <h3><?= e($t['teacher_name']) ?></h3>
                    <p><?= e($t['designation'] ?? 'Faculty') ?></p>
                    <p class="muted"><?= e($t['department_name']) ?></p>
                    <p class="muted">Source: <a href="<?= e($t['source_url']) ?>" target="_blank" rel="noopener">BUBT
                            official directory</a></p>
                </article><?php endforeach; ?></div><?php endif; ?><p class="notice">This is a public directory seed,
                not a teacher login list. Contact details and login credentials are not fabricated. Names/designations
                should be refreshed against the official directory before operational use.</p>
        </section>
    </main>
</body>

</html>