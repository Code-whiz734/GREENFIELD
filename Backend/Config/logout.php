<?php
session_start();
session_destroy();
header('Location: ../../Frontend/public/index.php');
?>
