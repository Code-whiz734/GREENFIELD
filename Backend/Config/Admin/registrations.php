<?php
include '../includes/db.php';

$sql = "SELECT users.fullname, courses.course_name
        FROM registrations
        JOIN users ON registrations.student_id = users.id
        JOIN courses ON registrations.course_id = courses.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrations</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h2>Student Registrations</h2>

<table>
<tr>
    <th>Student</th>
    <th>Course</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['fullname']; ?></td>
    <td><?php echo $row['course_name']; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>
