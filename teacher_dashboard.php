<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(empty($_SESSION['teacher_id'])){header('Location: teacher_login.php');exit;}
$st=$pdo->prepare('SELECT teacher_id,name,department,designation,email,phone,address FROM teachers WHERE teacher_id=?');
$st->execute([$_SESSION['teacher_id']]);$teacher=$st->fetch();
if(!$teacher){unset($_SESSION['teacher_id'],$_SESSION['teacher_name']);header('Location: teacher_login.php');exit;}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="assets/auth.css">
</head>

<body class="student-home">
    <header class="student-top"><a href="index.php"><img
                src="https://bubt.edu.bd/storage/uploads/settings/o5XcH3taBwJObXt99CtusV93oukdV8phVVxO4MQO.png" alt="">
            STUDENT PORTAL · TEACHER</a><a class="logout-btn" href="teacher_logout.php">Log out</a></header>
    <main class="student-welcome"><span class="eyebrow">TEACHER DASHBOARD</span>
        <h1>Welcome, <?= e($teacher['name']) ?>!</h1>
        <p class="intro">Your teacher account details.</p>
        <div class="student-info">
            <div><small>Teacher ID</small><strong><?= (int)$teacher['teacher_id'] ?></strong></div>
            <div><small>Name</small><strong><?= e($teacher['name']) ?></strong></div>
            <div><small>Department</small><strong><?= e($teacher['department']) ?></strong></div>
            <div><small>Designation</small><strong><?= e($teacher['designation']) ?></strong></div>
            <div><small>Email</small><strong><?= e($teacher['email']) ?></strong></div>
            <div><small>Phone</small><strong><?= e($teacher['phone']) ?></strong></div>
        </div>
        <a class="routine-btn" href="teacher_routine.php">View My Teaching Routine →</a><br><a class="routine-btn"
            href="view_routine.php">View Student Class Routine →</a>
    </main>
</body>

</html>