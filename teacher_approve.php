<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: teachers.php');exit;}
verify_csrf();$id=(int)($_POST['id']??0);
if($id>0){$st=$pdo->prepare("UPDATE teachers SET account_status='approved' WHERE teacher_id=?");$st->execute([$id]);}
header('Location: teachers.php');exit;
