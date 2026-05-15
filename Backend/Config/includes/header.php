<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$scriptDir = basename(dirname($_SERVER['SCRIPT_NAME']));
$root = $scriptDir === 'Admin' ? '..' : '.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greenfield Backend</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f7f7f7; }
        header { background: #004a7c; color: #fff; padding: 1rem; }
        nav a { color: #fff; margin-right: 1rem; text-decoration: none; }
        main { padding: 1.5rem; }
        .notice { margin: 1rem 0; padding: .8rem; background: #eef5fd; border-left: 4px solid #2f76b8; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: .75rem; text-align: left; border: 1px solid #ddd; }
        input, button { padding: .6rem; margin: .3rem 0; width: 100%; max-width: 400px; }
    </style>
</head>
<body>
<header>
    <strong>Greenfield Backend</strong>
    <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $root; ?>/Admin/dashboard.php">Admin Dashboard</a>
                <a href="<?php echo $root; ?>/Admin/add_course.php">Add Course</a>
                <a href="<?php echo $root; ?>/Admin/registrations.php">Registrations</a>
            <?php else: ?>
                <a href="<?php echo $root; ?>/dashboard.php">Dashboard</a>
                <a href="<?php echo $root; ?>/courses.php">Courses</a>
                <a href="<?php echo $root; ?>/My_courses.php">My Courses</a>
            <?php endif; ?>
            <a href="<?php echo $root; ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo $root; ?>/login.php?role=student">Student Login</a>
            <a href="<?php echo $root; ?>/login.php?role=admin">Admin Login</a>
            <a href="<?php echo $root; ?>/register.php?role=student">Student Register</a>
            <a href="<?php echo $root; ?>/register.php?role=admin">Admin Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
