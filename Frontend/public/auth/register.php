<?php
session_start();
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

    if ($selectedRole === 'admin') {
        $message = 'Admin accounts use one shared credential. Please use the admin login page.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match';
    } else {
        include '../includes/db.php';
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
    <h1>Greenfield Institute</h1>
    <p>Course Registration System</p>
    <div class="auth-actions">
        <a class="btn btn-secondary" href="login.php?role=student">Sign In</a>
        <a class="btn btn-primary" href="register.php?role=student">Register</a>
    </div>
    <?php if ($selectedRole === 'admin'): ?>
        <p class="form-note">Admin account creation is disabled because admin access is shared.</p>
    <?php else: ?>
    <form method="post" action="register.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">
        <div class="role-choice" aria-label="Choose account type">
            <label>
                <input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?> required>
                Student
            </label>
        </div>
        <label>
            Full name
            <input type="text" name="fullname" placeholder="John Doe" required>
        </label>
        <label>
            Email address
            <input type="email" name="email" placeholder="you@greenfield.edu" required>
        </label>
        <label>
            Password
            <input type="password" name="password" placeholder="Password" required>
        </label>
        <label>
            Confirm password
            <input type="password" name="confirm_password" placeholder="Confirm password" required>
        </label>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
    <?php endif; ?>
    <?php if ($message): ?>
        <p class="form-note error"><?php echo $message; ?></p>
    <?php endif; ?>
    <div class="auth-demo">
        <strong>Admin access:</strong>
        <span>All admins use <?php echo htmlspecialchars(ADMIN_EMAIL); ?> / <?php echo htmlspecialchars(ADMIN_PASSWORD); ?></span>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
