<?php
// Admin registration is intentionally restricted to an already authenticated admin.
session_start();
require_once 'config.php';
require_once 'functions.php';
require_admin();
header('Location: admin_form.php');
exit;
