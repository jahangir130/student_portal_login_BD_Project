<?php
require_once 'config.php';require_once 'functions.php';
$dept=trim($_GET['department']??'CSE');$intake=(int)($_GET['intake']??0);$section=(int)($_GET['section']??0);$rows=[];
$departments=$pdo->query('SELECT DISTINCT department FROM class_routines ORDER BY department')->fetchAll(PDO::FETCH_COLUMN);
$intakes=[]; $sections=[];
if($dept!==''){ $q=$pdo->prepare('SELECT DISTINCT intake_no FROM class_routines WHERE department=? AND intake_no IS NOT NULL ORDER BY intake_no'); $q->execute([$dept]); $intakes=$q->fetchAll(PDO::FETCH_COLUMN); }
if($dept!==''&&$intake>0){ $q=$pdo->prepare('SELECT DISTINCT section_no FROM class_routines WHERE department=? AND intake_no=? AND section_no IS NOT NULL ORDER BY section_no'); $q->execute([$dept,$intake]); $sections=$q->fetchAll(PDO::FETCH_COLUMN); }
if($dept!==''&&$intake>=1&&$intake<=65535&&$section>=1&&$section<=255){$st=$pdo->prepare('SELECT * FROM class_routines WHERE department=? AND intake_no=? AND section_no=? ORDER BY FIELD(class_day,"Saturday","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday"),start_time');$st->execute([$dept,$intake,$section]);$rows=$st->fetchAll();}
$days=['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];$periods=[['08:15:00','08:15 AM – 09:45 AM'],['09:45:00','09:45 AM – 11:15 AM'],['11:15:00','11:15 AM – 12:45 PM'],['13:15:00','01:15 PM – 02:45 PM'],['14:45:00','02:45 PM – 04:15 PM'],['16:15:00','04:15 PM – 05:45 PM']];$grid=[];foreach($rows as $r){$grid[$r['class_day']][$r['start_time']]=$r;}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Class Routine</title>
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
        min-width: 980px;
        border-collapse: collapse
    }

    .routine-grid th,
    .routine-grid td {
        border: 1px solid #d8dee5;
        padding: 10px 8px;
        text-align: center;
        vertical-align: middle
    }

    .routine-grid th {
        background: #eef2f5;
        font-size: 12px
    }

    .routine-grid td.day {
        background: #eef2f5;
        font-weight: 700
    }

    .routine-grid td.class-cell {
        background: #f0fbfa;
        min-width: 125px
    }

    .routine-grid small {
        display: block;
        color: #526174;
        margin-top: 4px
    }

    .routine-grid .break-cell {
        background: #fff6e5;
        font-weight: 700;
        color: #8a5a00
    }

    .routine-note {
        color: #64748b;
        font-size: 13px;
        margin-top: 12px
    }

    .routine-filter {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end
    }

    .routine-filter>div {
        min-width: 150px
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
            <div><strong>Student Portal</strong><small>STUDENT CLASS ROUTINE</small></div>
        </div>
        <h1>Find your class routine</h1>
        <p class="muted">Select your Department, then choose an Intake and Section that have published routine entries.
            Each period is 90 minutes.</p>
        <form class="filter-card routine-filter" method="GET">
            <div><label>Department</label><select name="department"><?php foreach($departments as $d): ?><option
                        value="<?= e($d) ?>" <?= $dept===$d?'selected':'' ?>><?= e($d) ?></option>
                    <?php endforeach; ?></select></div>
            <div><label>Intake</label><select name="intake">
                    <option value="">Select intake</option><?php foreach($intakes as $n): ?><option
                        value="<?= (int)$n ?>" <?= $intake===(int)$n?'selected':'' ?>>Intake <?= (int)$n ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label>Section</label><select name="section">
                    <option value="">Select section</option><?php foreach($sections as $n): ?><option
                        value="<?= (int)$n ?>" <?= $section===(int)$n?'selected':'' ?>>Section <?= (int)$n ?></option>
                    <?php endforeach; ?>
                </select></div><button class="btn primary">Show Routine</button>
        </form>
        <?php if($dept&&$intake&&$section): ?><section class="panel public-table">
            <div class="panel-head">
                <div>
                    <h3><?= e($dept) ?> · Intake <?= $intake ?> · Section <?= $section ?></h3>
                    <p>Weekly student timetable · 6 periods per day</p>
                </div>
            </div><?php if(!$rows): ?><p class="empty">No routine published for this intake and section.</p>
            <?php else: ?><div class="routine-grid">
                <table>
                    <thead>
                        <tr>
                            <th>Day / Time</th><?php foreach($periods as $i=>$p): ?><?php if($i===3): ?><th
                                class="break-cell">BREAK<br>12:45 PM – 01:15 PM</th><?php endif; ?><th><?= e($p[1]) ?>
                            </th><?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody><?php foreach($days as $day): ?><tr>
                            <td class="day"><?= e(strtoupper(substr($day,0,3))) ?></td>
                            <?php foreach($periods as $i=>$p): ?><?php if($i===3): ?><td class="break-cell">12:45
                                PM<br>–<br>01:15 PM</td><?php endif; ?><?php $r=$grid[$day][$p[0]]??null; ?><td
                                class="<?= $r?'class-cell':'' ?>">
                                <?php if($r): ?><strong><?= e($r['course_code']) ?></strong><small>Teacher:
                                    <?= e($r['teacher_name']) ?></small><small>Room:
                                    <?= e($r['room_no']) ?></small><?php else: ?>—<?php endif; ?></td>
                            <?php endforeach; ?>
                        </tr><?php endforeach; ?></tbody>
                </table>
            </div>
            <p class="routine-note">No classes are scheduled during the 12:45 PM–01:15 PM break.</p><?php endif; ?>
        </section><?php endif; ?>
    </main>
</body>

</html>