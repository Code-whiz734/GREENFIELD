<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: Admin/dashboard.php');
    exit;
}

include 'includes/header.php';
?>

<h2>Student Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
<ul>
    <li><a href="courses.php">Browse Courses</a></li>
    <li><a href="My_courses.php">My Registered Courses</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>

<?php include 'includes/footer.php'; ?>
