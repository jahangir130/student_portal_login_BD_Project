<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin('routines');
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: routines.php');exit;}
verify_csrf();$id=(int)($_POST['id']??0);if($id>0){$st=$pdo->prepare('DELETE FROM class_routines WHERE routine_id=?');$st->execute([$id]);}
header('Location: routines.php');exit;