<?php
session_start();
session_unset();
session_destroy();
include '../includes/config.php';
header('Location: ' . BASE_URL . '/index.php');
exit;
?>
