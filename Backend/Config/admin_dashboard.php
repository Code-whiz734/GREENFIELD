<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/header.php';
?>

<h2>Admin Dashboard</h2>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
<ul>
    <li><a href="add_course.php">Add Course</a></li>
    <li><a href="registrations.php">View Registrations</a></li>
</ul>

<?php include '../includes/footer.php'; ?>
