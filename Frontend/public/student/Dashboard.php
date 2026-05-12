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

// Load course count from XML
$xml = simplexml_load_file('../xml/courses.xml');
$total_courses = count($xml->course);

// Count student's registrations
$stmt = $conn->prepare('SELECT COUNT(*) AS enrolled FROM registrations WHERE student_id = ?');
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$enrolled = 0;
if ($row = $result->fetch_assoc()) {
    $enrolled = (int)$row['enrolled'];
}

// Determine available slots and registered courses
$total_registration = 0;
$stmt2 = $conn->prepare('SELECT COUNT(*) AS total FROM registrations');
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($row2 = $res2->fetch_assoc()) {
    $total_registration = (int)$row2['total'];
}

$available_courses = max(0, $total_courses - $total_registration);
?>
<section class="dashboard-hero">
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
    <article class="summary-card">
        <h3>Total available courses</h3>
        <p class="stat"><?php echo $total_courses; ?></p>
    </article>
    <article class="summary-card">
        <h3>Your enrolled courses</h3>
        <p class="stat"><?php echo $enrolled; ?></p>
    </article>
    <article class="summary-card">
        <h3>Open course slots</h3>
        <p class="stat"><?php echo $available_courses; ?></p>
    </article>
</section>
<section class="dashboard-grid">
    <article class="dashboard-card">
        <h2>Browse Courses</h2>
        <p>Explore available classes and register with one click.</p>
        <a class="btn btn-primary" href="Courses.php">View Courses</a>
    </article>
    <article class="dashboard-card">
        <h2>My Courses</h2>
        <p>Review your current enrollments and registration status.</p>
        <a class="btn btn-secondary" href="My_courses.php">My Courses</a>
    </article>
    <article class="dashboard-card">
        <h2>Account</h2>
        <p>Manage your account or logout when finished.</p>
        <a class="btn btn-secondary" href="../auth/logout.php">Logout</a>
    </article>
</section>
<?php include '../includes/footer.php'; ?>
