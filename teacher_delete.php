<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: teachers.php');exit;}
verify_csrf();$id=(int)($_POST['id']??0);
if($id>0){$stmt=$pdo->prepare('DELETE FROM teachers WHERE teacher_id=?');$stmt->execute([$id]);}
header('Location: teachers.php');exit;
