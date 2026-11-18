<?php
session_start();unset($_SESSION['teacher_id'],$_SESSION['teacher_name']);session_regenerate_id(true);header('Location: teacher_login.php');exit;