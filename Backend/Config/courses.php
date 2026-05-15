<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';
include 'includes/header.php';

$message = isset($_GET['message']) ? trim($_GET['message']) : '';
$result = $conn->query('SELECT * FROM courses ORDER BY course_name');
?>

<h2>Available Courses</h2>
<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Course Name</th>
            <th>Course Code</th>
            <th>Slots</th>
            <th>Action</th>
        </tr>
        <?php while ($course = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['slots']); ?></td>
                <td><a href="Register_course.php?id=<?php echo (int)$course['id']; ?>">Register</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No courses available at this time.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
