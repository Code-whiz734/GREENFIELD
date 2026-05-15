<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT courses.course_name, courses.course_code
     FROM registrations
     JOIN courses ON registrations.course_id = courses.id
     WHERE registrations.student_id = ?'
);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<h2>My Registered Courses</h2>

<?php if ($result->num_rows === 0): ?>
    <p>You have not registered for any courses yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Course Name</th>
            <th>Course Code</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['course_code']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
