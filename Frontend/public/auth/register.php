<?php
session_start();
include '../includes/db.php';
include '../includes/config.php';

$message = "";
$selectedRole = isset($_GET['role']) && in_array($_GET['role'], ['admin', 'student'], true) ? $_GET['role'] : '';

if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin'
        ? BASE_URL . '/admin/dashboard.php'
        : BASE_URL . '/student/Dashboard.php';
    header('Location: ' . $redirect);
    exit;
}

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $selectedRole = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';

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
            $insert = $conn->prepare('INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $fullname, $email, $hashedPassword, $selectedRole);

            if ($insert->execute()) {
                header('Location: ' . BASE_URL . '/auth/login.php?registered=1&role=' . $selectedRole);
                exit;
            }

            $message = 'Registration failed. Please try again.';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<section class="auth-card">
    <h1><?php echo $selectedRole === 'admin' ? 'Create Admin Account' : ($selectedRole === 'student' ? 'Create Student Account' : 'Create Account'); ?></h1>
    <p><?php echo $selectedRole ? 'Register now to create ' . ($selectedRole === 'admin' ? 'an admin' : 'a student') . ' account.' : 'Choose student or admin registration.'; ?></p>
    <div class="auth-actions">
        <a class="btn <?php echo $selectedRole === 'student' ? 'btn-primary' : 'btn-secondary'; ?>" href="register.php?role=student">Student Register</a>
        <a class="btn <?php echo $selectedRole === 'admin' ? 'btn-primary' : 'btn-secondary'; ?>" href="register.php?role=admin">Admin Register</a>
    </div>
    <form method="post" action="register.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">
        <div class="role-choice" aria-label="Choose account type">
            <label>
                <input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?> required>
                Student
            </label>
            <label>
                <input type="radio" name="role" value="admin" <?php echo $selectedRole === 'admin' ? 'checked' : ''; ?> required>
                Admin
            </label>
        </div>
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
    <p class="form-note">
        Already registered?
        <?php if ($selectedRole) { ?>
            <a href="login.php?role=<?php echo $selectedRole; ?>">Login as <?php echo $selectedRole === 'admin' ? 'an admin' : 'a student'; ?></a>
        <?php } else { ?>
            <a href="login.php?role=student">Student login</a> or <a href="login.php?role=admin">admin login</a>
        <?php } ?>
    </p>
</section>
<?php include '../includes/footer.php'; ?>
