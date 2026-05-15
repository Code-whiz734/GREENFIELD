<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

$sql = "SELECT users.fullname, courses.course_name
        FROM registrations
        JOIN users ON registrations.student_id = users.id
        JOIN courses ON registrations.course_id = courses.id";

$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>

<h2>Student Registrations</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Student</th>
            <th>Course</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No registrations found.</p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
