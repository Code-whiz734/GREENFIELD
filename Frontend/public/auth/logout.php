<?php
session_start();
session_unset();
session_destroy();
include '../includes/config.php';
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
?>