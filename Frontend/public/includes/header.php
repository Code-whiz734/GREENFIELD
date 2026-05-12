<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/config.php';
$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greenfield Institute</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/CSS/style.css">
</head>
<body>
<header class="site-header">
    <div class="site-brand">Greenfield Institute</div>
    <nav class="site-nav">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
        <?php if ($logged_in) { ?>
            <?php if ($_SESSION['role'] === 'admin') { ?>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/manage_courses.php">Manage Courses</a>
                <a href="<?php echo BASE_URL; ?>/admin/registrations.php">Registrations</a>
            <?php } else { ?>
                <a href="<?php echo BASE_URL; ?>/student/Dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/student/Courses.php">Courses</a>
                <a href="<?php echo BASE_URL; ?>/student/My_courses.php">My Courses</a>
            <?php } ?>
            <a href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a>
        <?php } else { ?>
            <a href="<?php echo BASE_URL; ?>/auth/login.php">Login</a>
            <a href="<?php echo BASE_URL; ?>/auth/register.php">Register</a>
        <?php } ?>
    </nav>
</header>
<main class="page-content">
