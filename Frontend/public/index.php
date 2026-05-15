<?php include 'includes/header.php'; ?>
<section class="hero-card home-page">
    <h1>Welcome to Greenfield</h1>
    <p>Choose how you want to access your dashboard.</p>
    <div class="home-actions">
        <article class="access-panel">
            <h2>Student</h2>
            <p>Browse courses and manage your registrations.</p>
            <div class="access-buttons">
                <a class="btn btn-primary" href="<?php echo BASE_URL; ?>/auth/login.php?role=student">Login</a>
                <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/auth/register.php?role=student">Register</a>
            </div>
        </article>
        <article class="access-panel">
            <h2>Admin</h2>
            <p>Manage courses, admins, and student registrations.</p>
            <div class="access-buttons">
                <a class="btn btn-primary" href="<?php echo BASE_URL; ?>/auth/login.php?role=admin">Login</a>
                <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>/auth/register.php?role=admin">Register</a>
            </div>
        </article>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
