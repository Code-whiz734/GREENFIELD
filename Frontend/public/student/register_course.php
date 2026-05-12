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

// Load courses from XML to verify course exists
$xml = simplexml_load_file('../xml/courses.xml');
$course_exists = false;
foreach ($xml->course as $course) {
    if ((int)$course->id === $course_id) {
        $course_exists = true;
        break;
    }
}

if (!$course_exists) {
    $message = 'Course not found.';
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
<?php include 'includes/header.php'; ?>
<section class="hero-card">
    <h1>Course Registration</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a class="btn btn-primary" href="Courses.php">Back to Courses</a>
</section>
<?php include 'includes/footer.php'; ?>