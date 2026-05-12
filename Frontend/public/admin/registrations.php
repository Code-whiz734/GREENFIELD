<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

// Get all registrations with student and course details
$query = "SELECT r.id, u.fullname, u.email, c.course_name, c.course_code, r.registration_date
          FROM registrations r
          JOIN users u ON r.student_id = u.id
          JOIN courses c ON r.course_id = c.id
          ORDER BY r.registration_date DESC";

$result = $conn->query($query);
$registrations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Student Management</p>
    <h1>Course Registrations</h1>
    <p>View all student course registrations and enrollment data.</p>
</section>

<section class="course-table-card">
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
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $reg): ?>
            <tr>
                <td><?php echo htmlspecialchars($reg['fullname']); ?></td>
                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                <td><?php echo htmlspecialchars($reg['course_name']); ?></td>
                <td><?php echo htmlspecialchars($reg['course_code']); ?></td>
                <td><?php echo htmlspecialchars($reg['registration_date'] ?? 'N/A'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="form-actions">
    <a class="btn btn-secondary" href="dashboard.php">← Back to Admin Dashboard</a>
</section>

<?php include '../includes/footer.php'; ?>