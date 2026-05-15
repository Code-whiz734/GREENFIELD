<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

$student_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

if ($course_id <= 0) {
    $message = 'Invalid course selection.';
} else {
    $stmt = $conn->prepare('SELECT id FROM registrations WHERE student_id = ? AND course_id = ?');
    $stmt->bind_param('ii', $student_id, $course_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = 'You already registered for this course.';
    } else {
        $stmt = $conn->prepare('INSERT INTO registrations (student_id, course_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $student_id, $course_id);
        $message = $stmt->execute() ? 'Course registered successfully.' : 'Registration failed.';
    }
}

header('Location: courses.php?message=' . urlencode($message));
exit;

