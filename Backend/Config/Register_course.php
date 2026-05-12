<?php
session_start();
include 'includes/db.php';

$student_id = $_SESSION['user_id'];
$course_id = $_GET['id'];

$check = $conn->query(
    "SELECT * FROM registrations
     WHERE student_id='$student_id'
     AND course_id='$course_id'"
);

if($check->num_rows > 0) {
    echo "You already registered for this course";
} else {
    $sql = "INSERT INTO registrations(student_id,course_id)
            VALUES('$student_id','$course_id')";

    if($conn->query($sql)) {
        echo "Course Registered Successfully";
    } else {
        echo "Registration Failed";
    }
}
?>
