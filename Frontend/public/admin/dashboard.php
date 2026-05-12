<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

// Get statistics
$user_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$registration_count = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];
?>

<section class="dashboard-hero">
    <div>
        <p class="eyebrow">Admin Dashboard</p>
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>.</h1>
        <p class="subtitle">Manage courses, view registrations, and oversee student activities.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="manage_courses.php">Manage Courses</a>
        <a class="btn btn-secondary" href="register_admin.php">Register Admin</a>
        <a class="btn btn-secondary" href="registrations.php">View Registrations</a>
    </div>
</section>

<section class="summary-grid">
    <article class="summary-card">
        <h3>Total Students</h3>
        <p class="stat"><?php echo $user_count; ?></p>
    </article>
    <article class="summary-card">
        <h3>Total Courses</h3>
        <p class="stat"><?php echo $course_count; ?></p>
    </article>
    <article class="summary-card">
        <h3>Total Registrations</h3>
        <p class="stat"><?php echo $registration_count; ?></p>
    </article>
</section>

<section class="dashboard-grid">
    <article class="dashboard-card">
        <h2>Course Management</h2>
        <p>View, add, edit, and delete courses. Manage your complete course catalog.</p>
        <a class="btn btn-primary" href="manage_courses.php">Manage Courses</a>
    </article>
    <article class="dashboard-card">
        <h2>Admin Management</h2>
        <p>Create new administrator accounts and manage admin privileges.</p>
        <a class="btn btn-secondary" href="register_admin.php">Register Admin</a>
    </article>
    <article class="dashboard-card">
        <h2>Student Registrations</h2>
        <p>View all student course registrations and enrollment data.</p>
        <a class="btn btn-secondary" href="registrations.php">View Registrations</a>
    </article>
</section>

<?php include '../includes/footer.php'; ?>