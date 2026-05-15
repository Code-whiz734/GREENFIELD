<?php
session_start();
include '../includes/db.php';
include '../includes/config.php';

$message = "";
$messageType = 'error';
$selectedRole = isset($_GET['role']) && in_array($_GET['role'], ['admin', 'student'], true) ? $_GET['role'] : '';

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

        $passwordMatches = password_verify($password, $user['password']);
        $needsRehash = $passwordMatches && password_needs_rehash($user['password'], PASSWORD_DEFAULT);

        if (!$passwordMatches && password_get_info($user['password'])['algo'] === 0) {
            $passwordMatches = hash_equals($user['password'], $password);
            $needsRehash = $passwordMatches;
        }

        if ($passwordMatches) {
            if ($needsRehash) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $update->bind_param('si', $newHash, $user['id']);
                $update->execute();
            }

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
    <h1><?php echo $selectedRole === 'admin' ? 'Admin Login' : ($selectedRole === 'student' ? 'Student Login' : 'Login'); ?></h1>
    <p><?php echo $selectedRole ? 'Sign in as ' . ($selectedRole === 'admin' ? 'an admin' : 'a student') . '.' : 'Choose student or admin access.'; ?></p>
    <div class="auth-actions">
        <a class="btn <?php echo $selectedRole === 'student' ? 'btn-primary' : 'btn-secondary'; ?>" href="login.php?role=student">Student Login</a>
        <a class="btn <?php echo $selectedRole === 'admin' ? 'btn-primary' : 'btn-secondary'; ?>" href="login.php?role=admin">Admin Login</a>
    </div>
    <form method="post" action="login.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">
        <div class="role-choice" aria-label="Choose login type">
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
    <p class="form-note">
        Don't have an account?
        <?php if ($selectedRole) { ?>
            <a href="register.php?role=<?php echo $selectedRole; ?>">Register as <?php echo $selectedRole === 'admin' ? 'an admin' : 'a student'; ?></a>
        <?php } else { ?>
            <a href="register.php?role=student">Student register</a> or <a href="register.php?role=admin">admin register</a>
        <?php } ?>
    </p>
</section>
<?php include '../includes/footer.php'; ?>
