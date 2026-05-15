<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$courses = [];

if ($query !== '') {
    $search = '%' . $query . '%';
    $stmt = $conn->prepare(
        'SELECT c.id, c.course_name, c.course_code, c.slots, COUNT(r.id) AS registered
         FROM courses c
         LEFT JOIN registrations r ON r.course_id = c.id
         WHERE c.course_name LIKE ? OR c.course_code LIKE ?
         GROUP BY c.id, c.course_name, c.course_code, c.slots
         ORDER BY c.course_name'
    );
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query(
        'SELECT c.id, c.course_name, c.course_code, c.slots, COUNT(r.id) AS registered
         FROM courses c
         LEFT JOIN registrations r ON r.course_id = c.id
         GROUP BY c.id, c.course_name, c.course_code, c.slots
         ORDER BY c.course_name'
    );
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['remaining_slots'] = max(0, (int)$row['slots'] - (int)$row['registered']);
        $courses[] = $row;
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Course catalog</p>
    <h1>Browse all available courses</h1>
    <p>Search by course name or code to quickly find the classes you want to register for.</p>
</section>

<section class="search-panel">
    <form action="Courses.php" method="get" class="search-form">
        <input type="search" name="q" placeholder="Search courses by name or code" value="<?php echo htmlspecialchars($query); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($query !== ''): ?>
            <a class="btn btn-secondary" href="Courses.php">Clear</a>
        <?php endif; ?>
    </form>
</section>

<section class="course-table-card">
    <?php if (count($courses) === 0): ?>
        <p class="empty-state">No courses match your search. Try a different keyword.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Code</th>
                <th>Slots</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($courses as $course): ?>
        <tr>
            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
            <td><?php echo htmlspecialchars($course['course_code']); ?></td>
            <td><?php echo htmlspecialchars($course['remaining_slots']); ?> of <?php echo htmlspecialchars($course['slots']); ?></td>
            <td>
                <?php if ($course['remaining_slots'] > 0): ?>
                    <a class="btn btn-primary" href="register_course.php?id=<?php echo htmlspecialchars($course['id']); ?>">
                        Register
                    </a>
                <?php else: ?>
                    <span class="form-note">Full</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
