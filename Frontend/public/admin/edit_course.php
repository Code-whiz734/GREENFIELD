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

$course = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $course_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
}

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

if (isset($_POST['update_course'])) {
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $slots = (int)$_POST['slots'];

    if (empty($course_name) || empty($course_code) || $slots <= 0) {
        $message = 'Please fill in all fields with valid values.';
    } else {
        $stmt = $conn->prepare('UPDATE courses SET course_name = ?, course_code = ?, slots = ? WHERE id = ?');
        $stmt->bind_param('ssii', $course_name, $course_code, $slots, $course_id);

        if ($stmt->execute()) {
            $message = 'Course updated successfully!';
            $messageType = 'success';

            // Refresh course data
            $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->bind_param('i', $course_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $course = $result->fetch_assoc();
        } else {
            $message = 'Failed to update course. Please try again.';
        }
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Course Management</p>
    <h1>Edit Course</h1>
    <p>Update course information and settings.</p>
</section>

<section class="auth-card">
    <h2>Course Details</h2>
    <form method="post" action="edit_course.php?id=<?php echo $course['id']; ?>">
        <label>
            Course Name
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
        </label>
        <label>
            Course Code
            <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
        </label>
        <label>
            Available Slots
            <input type="number" name="slots" value="<?php echo htmlspecialchars($course['slots']); ?>" min="1" required>
        </label>
        <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <p class="form-note"><a href="manage_courses.php">← Back to Course Management</a></p>
</section>

<?php include '../includes/footer.php'; ?>