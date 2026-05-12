<?php
include '../includes/db.php';

$message = "";

if(isset($_POST['add'])) {
    $name = $_POST['course_name'];
    $code = $_POST['course_code'];
    $slots = $_POST['slots'];

    $sql = "INSERT INTO courses(course_name,course_code,slots)
            VALUES('$name','$code','$slots')";

    if($conn->query($sql)) {
        $message = "Course Added";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Add Course</h2>

<form method="POST">
    <input type="text" name="course_name" placeholder="Course Name" required>
    <input type="text" name="course_code" placeholder="Course Code" required>
    <input type="number" name="slots" placeholder="Slots" required>

    <button type="submit" name="add">Add Course</button>
</form>

<p><?php echo $message; ?></p>

</body>
</html>
