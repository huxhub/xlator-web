<?php
/**
 * Xlator Admin - Logout Handler
 */
require_once __DIR__ . '/../includes/config.php';

$_SESSION['admin_logged_in'] = false;
unset($_SESSION['admin_logged_in'], $_SESSION['admin_user']);
session_destroy();

header('Location: /admin/login.php');
exit;
