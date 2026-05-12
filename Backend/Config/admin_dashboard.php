<?php
session_start();

if($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Admin Dashboard</h2>

<a class="btn" href="add_course.php">Add Course</a>
<a class="btn" href="registrations.php">View Registrations</a>

</body>
</html>
