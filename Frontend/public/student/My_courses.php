<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
include '../includes/db.php';
include '../includes/header.php';

$student_id = $_SESSION['user_id'];
?>
<section class="hero-card">
    <h1>My Registered Courses</h1>
    <p>These are the courses you have already enrolled in.</p>
</section>
<section class="course-table-card">
    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $courses = $conn->prepare(
                'SELECT c.course_name, c.course_code
                 FROM registrations r
                 INNER JOIN courses c ON c.id = r.course_id
                 WHERE r.student_id = ?
                 ORDER BY c.course_name'
            );
            $courses->bind_param('i', $student_id);
            $courses->execute();
            $course_result = $courses->get_result();
            ?>
            <?php if ($course_result->num_rows === 0): ?>
                <tr>
                    <td colspan="2">You have not registered for any courses yet.</td>
                </tr>
            <?php endif; ?>
            <?php while ($row = $course_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
<?php include '../includes/footer.php'; ?>
