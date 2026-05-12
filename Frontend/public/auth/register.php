<?php
session_start();
include '../includes/db.php';
include '../includes/config.php';

$message = "";

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/student/Dashboard.php');
    exit;
}

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = 'Passwords do not match';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = 'Email already exists';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)');
            $insert->bind_param('sss', $fullname, $email, $hashedPassword);

            if ($insert->execute()) {
                header('Location: ' . BASE_URL . '/auth/login.php?registered=1');
                exit;
            }

            $message = 'Registration failed. Please try again.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<section class="auth-card">
    <h1>Create your account</h1>
    <p>Register now to manage your course enrollments.</p>
    <form method="post" action="register.php">
        <label>
            Full name
            <input type="text" name="fullname" placeholder="John Doe" required>
        </label>
        <label>
            Email address
            <input type="email" name="email" placeholder="you@example.com" required>
        </label>
        <label>
            Password
            <input type="password" name="password" placeholder="Enter password" required>
        </label>
        <label>
            Confirm password
            <input type="password" name="confirm_password" placeholder="Repeat password" required>
        </label>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note error"><?php echo $message; ?></p>
    <?php endif; ?>
    <p class="form-note">Already registered? <a href="login.php">Login here</a></p>
</section>
<?php include '../includes/footer.php'; ?>