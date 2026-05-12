<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$student_id = $_SESSION['user_id'];

$sql = "SELECT courses.course_name, courses.course_code
        FROM registrations
        JOIN courses ON registrations.course_id = courses.id
        WHERE registrations.student_id='$student_id'";

$result = $conn->query($sql);
?>

<h2>My Registered Courses</h2>

<table>
<tr>
    <th>Course Name</th>
    <th>Course Code</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['course_name']; ?></td>
    <td><?php echo $row['course_code']; ?></td>
</tr>
<?php } ?>

</table>

<?php include 'includes/footer.php'; ?>
