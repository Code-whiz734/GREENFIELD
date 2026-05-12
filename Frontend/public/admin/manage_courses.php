<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

// Handle course deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $course_id = (int)$_GET['delete'];

    // Check if course has registrations
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM registrations WHERE course_id = ?");
    $check_stmt->bind_param('i', $course_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        $message = "Cannot delete course with existing registrations.";
        $messageType = 'error';
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $delete_stmt->bind_param('i', $course_id);
        if ($delete_stmt->execute()) {
            $message = "Course deleted successfully.";
            $messageType = 'success';
        } else {
            $message = "Failed to delete course.";
            $messageType = 'error';
        }
    }
}

// Get all courses
$courses = [];
$result = $conn->query("SELECT * FROM courses ORDER BY course_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Course Management</p>
    <h1>Manage All Courses</h1>
    <p>View, edit, and delete course offerings. Add new courses to expand your catalog.</p>
</section>

<?php if (isset($message)): ?>
<section class="message-card <?php echo $messageType; ?>">
    <p><?php echo $message; ?></p>
</section>
<?php endif; ?>

<section class="form-actions">
    <a class="btn btn-primary" href="add_course.php">+ Add New Course</a>
    <a class="btn btn-secondary" href="dashboard.php">← Back to Dashboard</a>
</section>

<section class="course-table-card">
    <?php if (count($courses) === 0): ?>
        <p class="empty-state">No courses found. <a href="add_course.php">Add your first course</a>.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Available Slots</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['slots']); ?></td>
                <td>
                    <a class="btn btn-small" href="edit_course.php?id=<?php echo $course['id']; ?>">Edit</a>
                    <a class="btn btn-small btn-danger" href="?delete=<?php echo $course['id']; ?>"
                       onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>