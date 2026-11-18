<?php
session_start();require_once 'config.php';require_once 'functions.php';
if(empty($_SESSION['student_id'])){header('Location: student_login.php');exit;}
$st=$pdo->prepare('SELECT s.id,s.name,s.dept,u.phn_num FROM student s JOIN `user` u ON u.id=s.id WHERE s.id=?');
$st->execute([$_SESSION['student_id']]);$student=$st->fetch();
if(!$student){session_destroy();header('Location: student_login.php');exit;}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Student Home</title><link rel="stylesheet" href="assets/auth.css"></head><body class="student-home">
<header class="student-top"><a href="student_dashboard.php"><img src="assets/portal-logo.jpg" alt=""> STUDENT PORTAL</a><a class="logout-btn" href="student_logout.php">Log out</a></header>
<main class="student-welcome"><span class="eyebrow">STUDENT DASHBOARD</span><h1>Welcome, <?= e($student['name']) ?>!</h1><p class="intro">Your student account is active.</p>
<div class="student-info"><div><small>Student ID</small><strong><?= e($student['id']) ?></strong></div><div><small>Name</small><strong><?= e($student['name']) ?></strong></div><div><small>Department</small><strong><?= e($student['dept']) ?></strong></div><div><small>Phone</small><strong><?= e($student['phn_num']) ?></strong></div></div>
<a class="routine-btn" href="view_routine.php">View Class Routine →</a></main></body></html>
