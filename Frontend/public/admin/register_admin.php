<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

include '../includes/header.php';
?>

<section class="hero-card">
    <p class="eyebrow">Admin Management</p>
    <h1>Shared Admin Access</h1>
    <p>All administrators now use the same system credential.</p>
</section>

<section class="auth-card">
    <h2>Admin Credential</h2>
    <p class="form-note">Admin account creation is disabled. Update the shared credential in <code>Frontend/public/includes/config.php</code>.</p>
    <p class="form-note">Email: <strong><?php echo htmlspecialchars(ADMIN_EMAIL); ?></strong></p>
    <p class="form-note">Password: <strong><?php echo htmlspecialchars(ADMIN_PASSWORD); ?></strong></p>
    <p class="form-note"><a href="dashboard.php">Back to Admin Dashboard</a></p>
</section>

<?php include '../includes/footer.php'; ?>
