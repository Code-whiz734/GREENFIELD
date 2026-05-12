<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$message = "";
$messageType = 'error';

if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $slots = (int)$_POST['slots'];

    if (empty($course_name) || empty($course_code) || $slots <= 0) {
        $message = 'Please fill in all fields with valid values.';
    } else {
        $stmt = $conn->prepare('INSERT INTO courses (course_name, course_code, slots) VALUES (?, ?, ?)');
        $stmt->bind_param('ssi', $course_name, $course_code, $slots);

        if ($stmt->execute()) {
            $message = 'Course added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add course. Please try again.';
        }
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Course Management</p>
    <h1>Add New Course</h1>
    <p>Create a new course offering for students to register.</p>
</section>

<section class="auth-card">
    <h2>Add Course Details</h2>
    <form method="post" action="add_course.php">
        <label>
            Course Name
            <input type="text" name="course_name" placeholder="e.g., Web Development" required>
        </label>
        <label>
            Course Code
            <input type="text" name="course_code" placeholder="e.g., WD101" required>
        </label>
        <label>
            Available Slots
            <input type="number" name="slots" placeholder="30" min="1" required>
        </label>
        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <p class="form-note"><a href="dashboard.php">← Back to Admin Dashboard</a></p>
</section>

<?php include '../includes/footer.php'; ?>