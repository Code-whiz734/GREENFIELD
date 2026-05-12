<?php include 'includes/header.php'; ?>
<section class="hero-card home-page">
    <h1>Welcome to Greenfield</h1>
    <p>Choose how you want to access your dashboard.</p>
    <div class="home-actions">
        <a class="btn btn-primary" href="<?php echo BASE_URL; ?>/auth/login.php?role=student">Student Login</a>
        <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/auth/login.php?role=admin">Admin Login</a>
        <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/auth/register.php">Register as Student</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
