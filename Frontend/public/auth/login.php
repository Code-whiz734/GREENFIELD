<?php
session_start();
include '../includes/db.php';
include '../includes/config.php';

$message = "";
$messageType = 'error';
$selectedRole = isset($_GET['role']) && $_GET['role'] === 'admin' ? 'admin' : 'student';

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $message = 'Registration successful. Please login.';
    $messageType = 'success';
}

if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin'
        ? BASE_URL . '/admin/dashboard.php'
        : BASE_URL . '/student/Dashboard.php';
    header('Location: ' . $redirect);
    exit;
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $selectedRole = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';
    $messageType = 'error';

    $stmt = $conn->prepare('SELECT id, fullname, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['role'] !== $selectedRole) {
                $message = 'This account is registered as a ' . $user['role'] . '. Please choose the correct login type.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                $redirect = $user['role'] === 'admin'
                    ? BASE_URL . '/admin/dashboard.php'
                    : BASE_URL . '/student/Dashboard.php';
                header('Location: ' . $redirect);
                exit;
            }
        }
    }

    if (!$message) {
        $message = 'Invalid email or password';
    }
}
?>
<?php include '../includes/header.php'; ?>
<section class="auth-card">
    <h1>Login</h1>
    <p>Sign in as a student or administrator.</p>
    <form method="post" action="login.php">
        <div class="role-choice" aria-label="Choose login type">
            <label>
                <input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?>>
                Student
            </label>
            <label>
                <input type="radio" name="role" value="admin" <?php echo $selectedRole === 'admin' ? 'checked' : ''; ?>>
                Admin
            </label>
        </div>
        <label>
            Email address
            <input type="email" name="email" placeholder="you@example.com" required>
        </label>
        <label>
            Password
            <input type="password" name="password" placeholder="Enter password" required>
        </label>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <p class="form-note">Don't have a student account? <a href="register.php">Create one</a></p>
</section>
<?php include '../includes/footer.php'; ?>
