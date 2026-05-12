<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$message = "";
$messageType = 'error';

if (isset($_POST['register_admin'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $message = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = 'Email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, "admin")');
            $stmt->bind_param('sss', $fullname, $email, $hashedPassword);

            if ($stmt->execute()) {
                $message = 'Admin account created successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to create admin account. Please try again.';
            }
        }
    }
}
?>

<section class="hero-card">
    <p class="eyebrow">Admin Management</p>
    <h1>Register New Admin</h1>
    <p>Create a new administrator account for the system.</p>
</section>

<section class="auth-card">
    <h2>Admin Account Details</h2>
    <form method="post" action="register_admin.php">
        <label>
            Full Name
            <input type="text" name="fullname" placeholder="John Doe" required>
        </label>
        <label>
            Email Address
            <input type="email" name="email" placeholder="admin@example.com" required>
        </label>
        <label>
            Password
            <input type="password" name="password" placeholder="Enter password" minlength="6" required>
        </label>
        <label>
            Confirm Password
            <input type="password" name="confirm_password" placeholder="Repeat password" minlength="6" required>
        </label>
        <button type="submit" name="register_admin" class="btn btn-primary">Create Admin Account</button>
    </form>
    <?php if ($message): ?>
        <p class="form-note <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <p class="form-note"><a href="dashboard.php">← Back to Admin Dashboard</a></p>
</section>

<?php include '../includes/footer.php'; ?>