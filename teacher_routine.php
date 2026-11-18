<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(empty($_SESSION['teacher_id'])){header('Location: teacher_login.php');exit;}
$st=$pdo->prepare('SELECT name,department FROM teachers WHERE teacher_id=?');$st->execute([$_SESSION['teacher_id']]);$teacher=$st->fetch();if(!$teacher){header('Location: teacher_login.php');exit;}
$st=$pdo->prepare('SELECT * FROM class_routines WHERE LOWER(TRIM(teacher_name))=LOWER(TRIM(?)) ORDER BY FIELD(class_day,"Saturday","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday"),start_time');$st->execute([$teacher['name']]);$rows=$st->fetchAll();
$days=['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];$periods=[['08:15:00','08:15 AM – 09:45 AM'],['09:45:00','09:45 AM – 11:15 AM'],['11:15:00','11:15 AM – 12:45 PM'],['13:15:00','01:15 PM – 02:45 PM'],['14:45:00','02:45 PM – 04:15 PM'],['16:15:00','04:15 PM – 05:45 PM']];$grid=[];foreach($rows as $r){$grid[$r['class_day']][$r['start_time']]=$r;}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Teaching Routine</title>
    <link rel="stylesheet" href="assets/admin.css">
    <style>
    .routine-grid {
        overflow: auto;
        background: #fff;
        border: 1px solid #d9e0e7;
        border-radius: 14px;
        margin-top: 22px
    }

    .routine-grid table {
        width: 100%;
        min-width: 950px;
        border-collapse: collapse
    }

    .routine-grid th,
    .routine-grid td {
        border: 1px solid #d8dee5;
        padding: 12px 8px;
        text-align: center;
        vertical-align: middle
    }

    .routine-grid th,
    .routine-grid td.day {
        background: #eef2f5
    }

    .routine-grid td.day {
        font-weight: 700
    }

    .routine-grid td.class-cell {
        background: #f0fbfa;
        min-width: 130px
    }

    .routine-grid small {
        display: block;
        color: #526174;
        margin-top: 5px
    }

    .empty-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        margin-top: 20px
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

<body class="admin-body"><a class="bubt-global-brand" href="index.php"><img
            src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png"
            alt="BUBT Logo"><span>Bangladesh University of Business &amp; Technology</span></a>
    <main class="routine-public">
        <div class="public-brand"><span class="brand-icon">SP</span>
            <div><strong>Student Portal</strong><small>TEACHER ROUTINE</small></div>
        </div>
        <h1>My Teaching Routine</h1>
        <p class="muted"><?= e($teacher['name']) ?> · <?= e($teacher['department']) ?></p><?php if(!$rows): ?><div
            class="empty-card">No classes have been assigned to your teacher account yet. Please contact the
            administrator.</div><?php else: ?><div class="routine-grid">
            <table>
                <thead>
                    <tr>
                        <th>Day / Time</th><?php foreach($periods as $i=>$p): ?><?php if($i===3): ?><th>BREAK<br>12:45
                            PM – 01:15 PM</th><?php endif; ?><th><?= e($p[1]) ?></th><?php endforeach; ?>
                    </tr>
                </thead>
                <tbody><?php foreach($days as $day): ?><tr>
                        <td class="day"><?= e(strtoupper(substr($day,0,3))) ?></td>
                        <?php foreach($periods as $i=>$p): ?><?php if($i===3): ?><td>Break</td>
                        <?php endif; ?><?php $r=$grid[$day][$p[0]]??null; ?><td class="<?= $r?'class-cell':'' ?>">
                            <?php if($r): ?><strong><?= e($r['course_code']) ?></strong><small><?= e($r['department'].' · '.$r['semester']) ?></small><small><?= e($r['room_no']) ?></small><?php else: ?>—<?php endif; ?>
                        </td><?php endforeach; ?>
                    </tr><?php endforeach; ?></tbody>
            </table>
        </div><?php endif; ?><p style="margin-top:18px"><a href="teacher_dashboard.php">← Back to dashboard</a></p>
    </main>
</body>

</html>