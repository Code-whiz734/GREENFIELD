<?php
session_start();
include 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'Admin/dashboard.php' : 'dashboard.php'));
    exit;
}

$message = "";
$selectedRole = isset($_GET['role']) && in_array($_GET['role'], ['admin', 'student'], true) ? $_GET['role'] : '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $selectedRole = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';

    if ($selectedRole === 'admin') {
        if (hash_equals(ADMIN_EMAIL, $email) && hash_equals(ADMIN_PASSWORD, $password)) {
            $_SESSION['user_id'] = 0;
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = ADMIN_NAME;
            header('Location: Admin/dashboard.php');
            exit;
        }

        $message = 'Invalid admin email or password';
    } else {
        $stmt = $conn->prepare('SELECT id, fullname, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
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
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['name'] = $user['fullname'];
                    header('Location: dashboard.php');
                    exit;
                }
            }

            if (!$message) {
                $message = 'Invalid password';
            }
        } else {
            $message = 'User not found';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2><?php echo $selectedRole === 'admin' ? 'Admin Login' : ($selectedRole === 'student' ? 'Student Login' : 'Login'); ?></h2>

<p>
    <a href="login.php?role=student">Student Login</a> |
    <a href="login.php?role=admin">Admin Login</a>
</p>

<form method="POST" action="login.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">
    <label>
        <input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?> required>
        Student
    </label>
    <label>
        <input type="radio" name="role" value="admin" <?php echo $selectedRole === 'admin' ? 'checked' : ''; ?> required>
        Admin
    </label>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
