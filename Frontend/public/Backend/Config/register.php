<?php
include 'includes/db.php';

$message = "";

if(isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0) {
        $message = "Email already exists";
    } else {
        $sql = "INSERT INTO users(fullname,email,password)
                VALUES('$fullname','$email','$password')";

        if($conn->query($sql)) {
            $message = "Registration Successful";
        } else {
            $message = "Registration Failed";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2>Student Registration</h2>

<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<p><?php echo $message; ?></p>

<?php include 'includes/footer.php'; ?>
