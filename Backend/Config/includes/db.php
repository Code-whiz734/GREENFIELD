<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "greenfield";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', 'admin@greenfield.edu');
}

if (!defined('ADMIN_PASSWORD')) {
    define('ADMIN_PASSWORD', 'admin@23');
}

if (!defined('ADMIN_NAME')) {
    define('ADMIN_NAME', 'Greenfield Admin');
}
?>
