<?php
session_start();
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

    if ($selectedRole === 'admin') {
        if (hash_equals(ADMIN_EMAIL, $email) && hash_equals(ADMIN_PASSWORD, $password)) {
            $_SESSION['user_id'] = 0;
            $_SESSION['name'] = ADMIN_NAME;
            $_SESSION['role'] = 'admin';
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }

        $message = 'Invalid admin email or password';
    } else {
        include '../includes/db.php';
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

                if ($user['role'] !== 'student') {
                    $message = 'Please use the shared admin login details.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];
                    header('Location: ' . BASE_URL . '/student/Dashboard.php');
                    exit;
                }
            }
        }

        if (!$message) {
            $message = 'Invalid email or password';
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<section class="auth-card">
    <h1>Greenfield Institute</h1>
    <p>Course Registration System</p>
    <div class="auth-actions">
        <a class="btn btn-primary" href="login.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">Sign In</a>
        <a class="btn btn-secondary" href="register.php?role=student">Register</a>
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
            <input type="email" name="email" placeholder="you@greenfield.edu" required>
        </label>
        <label>
            Password
            <input type="password" name="password" placeholder="Password" required>
        </label>
        <button type="submit" name="login" class="btn btn-primary">Sign In</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <div class="auth-demo">
        <strong>Demo accounts:</strong>
        <span>Harvey Spector - <?php echo htmlspecialchars(ADMIN_EMAIL); ?> / <?php echo htmlspecialchars(ADMIN_PASSWORD); ?></span>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
