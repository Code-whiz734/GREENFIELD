<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'student') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$student_id = $_SESSION['user_id'];

$total_courses = 0;
$course_count_result = $conn->query('SELECT COUNT(*) AS total FROM courses');
if ($row = $course_count_result->fetch_assoc()) {
    $total_courses = (int)$row['total'];
}

// Count student's registrations
$stmt = $conn->prepare('SELECT COUNT(*) AS enrolled FROM registrations WHERE student_id = ?');
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$enrolled = 0;
if ($row = $result->fetch_assoc()) {
    $enrolled = (int)$row['enrolled'];
}

$available_courses = 0;
$slot_result = $conn->query('SELECT COALESCE(SUM(slots), 0) AS total_slots FROM courses');
$registration_result = $conn->query('SELECT COUNT(*) AS used_slots FROM registrations');
if ($row = $slot_result->fetch_assoc()) {
    $available_courses = (int)$row['total_slots'];
}
if ($row = $registration_result->fetch_assoc()) {
    $available_courses = max(0, $available_courses - (int)$row['used_slots']);
}
?>
<section class="dashboard-hero student-hero">
    <div>
        <p class="eyebrow">Student Dashboard</p>
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>.</h1>
        <p class="subtitle">Manage your registrations, review current courses, and stay on top of your learning path.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="Courses.php">Browse Courses</a>
        <a class="btn btn-secondary" href="My_courses.php">My Courses</a>
    </div>
</section>
<section class="summary-grid">
    <article class="summary-card metric-blue">
        <span class="metric-label">Catalog</span>
        <h3>Total available courses</h3>
        <p class="stat"><?php echo $total_courses; ?></p>
    </article>
    <article class="summary-card metric-green">
        <span class="metric-label">Enrolled</span>
        <h3>Your enrolled courses</h3>
        <p class="stat"><?php echo $enrolled; ?></p>
    </article>
    <article class="summary-card metric-amber">
        <span class="metric-label">Open</span>
        <h3>Open course slots</h3>
        <p class="stat"><?php echo $available_courses; ?></p>
    </article>
</section>
<section class="dashboard-grid">
    <article class="dashboard-card">
        <span class="card-kicker">Explore</span>
        <h2>Browse Courses</h2>
        <p>Explore available classes and register with one click.</p>
        <a class="btn btn-primary" href="Courses.php">View Courses</a>
    </article>
    <article class="dashboard-card">
        <span class="card-kicker">Progress</span>
        <h2>My Courses</h2>
        <p>Review your current enrollments and registration status.</p>
        <a class="btn btn-secondary" href="My_courses.php">My Courses</a>
    </article>
    <article class="dashboard-card">
        <span class="card-kicker">Session</span>
        <h2>Account</h2>
        <p>Manage your account or logout when finished.</p>
        <a class="btn btn-secondary" href="../auth/logout.php">Logout</a>
    </article>
</section>
<?php include '../includes/footer.php'; ?>
