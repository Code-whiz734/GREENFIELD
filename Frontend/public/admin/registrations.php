<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$message = '';
$messageType = 'success';

if (isset($_GET['delete_student']) && is_numeric($_GET['delete_student'])) {
    $studentId = (int)$_GET['delete_student'];

    $studentCheck = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'student'");
    $studentCheck->bind_param('i', $studentId);
    $studentCheck->execute();
    $studentCheck->store_result();

    if ($studentCheck->num_rows === 0) {
        $message = 'Student account not found.';
        $messageType = 'error';
    } else {
        $deleteRegistrations = $conn->prepare('DELETE FROM registrations WHERE student_id = ?');
        $deleteRegistrations->bind_param('i', $studentId);
        $deleteRegistrations->execute();

        $deleteStudent = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $deleteStudent->bind_param('i', $studentId);

        if ($deleteStudent->execute()) {
            $message = 'Student removed successfully.';
        } else {
            $message = 'Failed to remove student.';
            $messageType = 'error';
        }
    }
}

$students = [];
$studentResult = $conn->query(
    "SELECT u.id,
            u.fullname,
            u.email,
            COUNT(r.id) AS registered_courses,
            GROUP_CONCAT(CONCAT(c.course_name, ' (', c.course_code, ')') ORDER BY c.course_name SEPARATOR ', ') AS course_list
     FROM users u
     LEFT JOIN registrations r ON r.student_id = u.id
     LEFT JOIN courses c ON c.id = r.course_id
     WHERE u.role = 'student'
     GROUP BY u.id, u.fullname, u.email
     ORDER BY u.fullname"
);
if ($studentResult) {
    while ($row = $studentResult->fetch_assoc()) {
        $students[] = $row;
    }
}

$registrations = [];
$registrationResult = $conn->query(
    "SELECT r.id, u.fullname, u.email, c.course_name, c.course_code
     FROM registrations r
     LEFT JOIN users u ON r.student_id = u.id
     LEFT JOIN courses c ON r.course_id = c.id
     ORDER BY u.fullname, c.course_name"
);
if ($registrationResult) {
    while ($row = $registrationResult->fetch_assoc()) {
        $registrations[] = $row;
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Student Management</p>
    <h1>Students & Course Registrations</h1>
    <p>View registered students and the courses they have enrolled in.</p>
</section>

<?php if ($message): ?>
<section class="message-card <?php echo $messageType; ?>">
    <p><?php echo htmlspecialchars($message); ?></p>
</section>
<?php endif; ?>

<section class="summary-grid">
    <article class="summary-card metric-blue">
        <span class="metric-label">Students</span>
        <h3>Total Students</h3>
        <p class="stat"><?php echo count($students); ?></p>
    </article>
    <article class="summary-card metric-green">
        <span class="metric-label">Enrollment</span>
        <h3>Total Registrations</h3>
        <p class="stat"><?php echo count($registrations); ?></p>
    </article>
</section>

<section class="course-table-card">
    <h2>All Students</h2>
    <?php if (count($students) === 0): ?>
        <p class="empty-state">No student accounts found.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Registered Courses</th>
                <th>Courses Registered</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['registered_courses']); ?></td>
                <td><?php echo htmlspecialchars($student['course_list'] ?: 'No courses yet'); ?></td>
                <td>
                    <a class="btn btn-small btn-danger"
                       href="registrations.php?delete_student=<?php echo (int)$student['id']; ?>"
                       onclick="return confirm('Remove this student and all their course registrations?')">
                        Remove
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="course-table-card">
    <h2>Course Registrations</h2>
    <?php if (count($registrations) === 0): ?>
        <p class="empty-state">No registrations found.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Course Code</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $reg): ?>
            <tr>
                <td><?php echo htmlspecialchars($reg['fullname'] ?? 'Unknown student'); ?></td>
                <td><?php echo htmlspecialchars($reg['email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($reg['course_name'] ?? 'Unknown course'); ?></td>
                <td><?php echo htmlspecialchars($reg['course_code'] ?? 'N/A'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="form-actions">
    <a class="btn btn-secondary" href="dashboard.php">Back to Admin Dashboard</a>
</section>

<?php include '../includes/footer.php'; ?>
