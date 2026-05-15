<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';
$message = "";

if (isset($_POST['add'])) {
    $name = trim($_POST['course_name']);
    $code = trim($_POST['course_code']);
    $slots = (int)$_POST['slots'];

    $stmt = $conn->prepare('INSERT INTO courses (course_name, course_code, slots) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $name, $code, $slots);

    if ($stmt->execute()) {
        $message = 'Course added successfully.';
    } else {
        $message = 'Failed to add course.';
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Add Course</h2>

<form method="POST">
    <input type="text" name="course_name" placeholder="Course Name" required>
    <input type="text" name="course_code" placeholder="Course Code" required>
    <input type="number" name="slots" placeholder="Slots" min="1" required>
    <button type="submit" name="add">Add Course</button>
</form>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
