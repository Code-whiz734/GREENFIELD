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
$sql = "SELECT course_id FROM registrations WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Load courses from XML
$xml = simplexml_load_file('../xml/courses.xml');
$courses = [];
foreach ($xml->course as $course) {
    $courses[(int)$course->id] = [
        'name' => (string)$course->name,
        'code' => (string)$course->code
    ];
}
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
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $course_id = $row['course_id']; ?>
                <?php if (isset($courses[$course_id])): ?>
                <tr>
                    <td><?php echo htmlspecialchars($courses[$course_id]['name']); ?></td>
                    <td><?php echo htmlspecialchars($courses[$course_id]['code']); ?></td>
                </tr>
                <?php endif; ?>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
<?php include 'includes/footer.php'; ?>