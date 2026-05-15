<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: Courses.php');
    exit;
}

include '../includes/db.php';

$student_id = $_SESSION['user_id'];
$course_id = (int)$_GET['id'];

$course = null;
$course_stmt = $conn->prepare(
    'SELECT c.id, c.slots, COUNT(r.id) AS registered
     FROM courses c
     LEFT JOIN registrations r ON r.course_id = c.id
     WHERE c.id = ?
     GROUP BY c.id, c.slots'
);
$course_stmt->bind_param('i', $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
if ($course_result) {
    $course = $course_result->fetch_assoc();
}

if (!$course) {
    $message = 'Course not found.';
} elseif ((int)$course['registered'] >= (int)$course['slots']) {
    $message = 'This course is already full.';
} else {
    $check = $conn->prepare('SELECT id FROM registrations WHERE student_id = ? AND course_id = ?');
    $check->bind_param('ii', $student_id, $course_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = 'You already registered for this course.';
    } else {
        $insert = $conn->prepare('INSERT INTO registrations (student_id, course_id) VALUES (?, ?)');
        $insert->bind_param('ii', $student_id, $course_id);

        if ($insert->execute()) {
            $message = 'Course registered successfully.';
        } else {
            $message = 'Registration failed. Please try again.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<section class="hero-card">
    <h1>Course Registration</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a class="btn btn-primary" href="Courses.php">Back to Courses</a>
</section>
<?php include '../includes/footer.php'; ?>
