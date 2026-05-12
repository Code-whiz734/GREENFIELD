<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/header.php';

$xml = simplexml_load_file('../xml/courses.xml');
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$courses = [];
foreach ($xml->course as $course) {
    if ($query === '' || stripos($course->name . ' ' . $course->code, $query) !== false) {
        $courses[] = $course;
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
            <td><?php echo htmlspecialchars($course->name); ?></td>
            <td><?php echo htmlspecialchars($course->code); ?></td>
            <td><?php echo htmlspecialchars($course->slots); ?></td>
            <td>
                <a class="btn btn-primary" href="register_course.php?id=<?php echo htmlspecialchars($course->id); ?>">
                    Register
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
