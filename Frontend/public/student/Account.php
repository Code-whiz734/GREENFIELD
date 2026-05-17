<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'student') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$student_id = (int)$_SESSION['user_id'];
$message = '';
$messageType = 'success';

$student = [
    'fullname' => '',
    'email' => '',
    'role' => 'student',
];

function load_student(mysqli $conn, int $student_id): array
{
    $stmt = $conn->prepare("SELECT fullname, email, role FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row;
    }

    return [
        'fullname' => '',
        'email' => '',
        'role' => 'student',
    ];
}

$student = load_student($conn, $student_id);

if (isset($_POST['update_account'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullname === '' || $email === '') {
        $message = 'Full name and email address are required.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    } else {
        $duplicate = $conn->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
        $duplicate->bind_param('si', $email, $student_id);
        $duplicate->execute();
        $duplicate->store_result();

        if ($duplicate->num_rows > 0) {
            $message = 'That email address is already in use.';
            $messageType = 'error';
        } else {
            $canUpdate = true;

            if ($newPassword !== '') {
                $passwordStmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
                $passwordStmt->bind_param('i', $student_id);
                $passwordStmt->execute();
                $passwordResult = $passwordStmt->get_result();
                $passwordRow = $passwordResult->fetch_assoc();
                $storedPassword = $passwordRow['password'] ?? '';
                $passwordMatches = password_verify($currentPassword, $storedPassword);

                if (!$passwordMatches && password_get_info($storedPassword)['algo'] === 0) {
                    $passwordMatches = hash_equals($storedPassword, $currentPassword);
                }

                if (!$passwordMatches) {
                    $message = 'Enter your current password to change your password.';
                    $messageType = 'error';
                    $canUpdate = false;
                }
            }

            if ($canUpdate) {
                if ($newPassword !== '') {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $update = $conn->prepare('UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?');
                    $update->bind_param('sssi', $fullname, $email, $hashedPassword, $student_id);
                } else {
                    $update = $conn->prepare('UPDATE users SET fullname = ?, email = ? WHERE id = ?');
                    $update->bind_param('ssi', $fullname, $email, $student_id);
                }

                if ($update->execute()) {
                    $_SESSION['name'] = $fullname;
                    $student = load_student($conn, $student_id);
                    $message = 'Account details updated successfully.';
                    $messageType = 'success';
                } else {
                    $message = 'Could not update your account. Please try again.';
                    $messageType = 'error';
                }
            }
        }
    }
}
?>
<section class="hero-card">
    <p class="eyebrow">Account</p>
    <h1>Manage your account</h1>
    <p>Review your personal details and update your login information.</p>
</section>

<?php if ($message): ?>
<section class="message-card <?php echo $messageType; ?>">
    <p><?php echo htmlspecialchars($message); ?></p>
</section>
<?php endif; ?>

<section class="profile-panel" aria-labelledby="personal-details-title">
    <div>
        <span class="card-kicker">Profile</span>
        <h2 id="personal-details-title">Personal details</h2>
    </div>
    <dl class="profile-details">
        <div>
            <dt>Full name</dt>
            <dd><?php echo htmlspecialchars($student['fullname']); ?></dd>
        </div>
        <div>
            <dt>Email address</dt>
            <dd><?php echo htmlspecialchars($student['email']); ?></dd>
        </div>
        <div>
            <dt>Account type</dt>
            <dd><?php echo htmlspecialchars(ucfirst($student['role'])); ?></dd>
        </div>
        <div>
            <dt>Student ID</dt>
            <dd><?php echo htmlspecialchars((string)$student_id); ?></dd>
        </div>
    </dl>
</section>

<section class="account-form-card" aria-labelledby="update-account-title">
    <h2 id="update-account-title">Update details</h2>
    <form method="post" action="Account.php">
        <label>
            Full name
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required>
        </label>
        <label>
            Email address
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </label>
        <label>
            Current password
            <input type="password" name="current_password" placeholder="Required only when changing password">
        </label>
        <label>
            New password
            <input type="password" name="new_password" placeholder="Leave blank to keep current password">
        </label>
        <label>
            Confirm new password
            <input type="password" name="confirm_password" placeholder="Repeat new password">
        </label>
        <div class="form-actions">
            <button type="submit" name="update_account" class="btn btn-primary">Save changes</button>
            <a class="btn btn-secondary" href="Dashboard.php">Back to Dashboard</a>
            <a class="btn btn-secondary" href="../auth/logout.php">Logout</a>
        </div>
    </form>
</section>
<?php include '../includes/footer.php'; ?>
