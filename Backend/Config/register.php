<?php
session_start();
include 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = "";
$selectedRole = isset($_GET['role']) && in_array($_GET['role'], ['admin', 'student'], true) ? $_GET['role'] : '';

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';
    $selectedRole = $role;

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = 'Email already exists';
    } else {
        $stmt = $conn->prepare('INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $fullname, $email, $password, $role);

        if ($stmt->execute()) {
            header('Location: login.php?role=' . $role);
            exit;
        }

        $message = 'Registration failed. Please try again.';
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2><?php echo $selectedRole === 'admin' ? 'Admin Registration' : ($selectedRole === 'student' ? 'Student Registration' : 'Registration'); ?></h2>

<p>
    <a href="register.php?role=student">Student Register</a> |
    <a href="register.php?role=admin">Admin Register</a>
</p>

<form method="POST" action="register.php<?php echo $selectedRole ? '?role=' . $selectedRole : ''; ?>">
    <label>
        <input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?> required>
        Student
    </label>
    <label>
        <input type="radio" name="role" value="admin" <?php echo $selectedRole === 'admin' ? 'checked' : ''; ?> required>
        Admin
    </label>
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
</form>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
