<?php
// Admin Setup Script
// Run this once to create an initial admin user
// Then delete this file for security

include 'includes/db.php';

$admin_name = 'Administrator';
$admin_email = 'admin@greenfield.edu';
$admin_password = 'admin123'; // Change this!

$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->bind_param('sss', $admin_name, $admin_email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Email: $admin_email<br>";
    echo "Password: $admin_password<br>";
    echo "<strong>IMPORTANT: Change the password after first login and delete this file!</strong>";
} else {
    echo "Error creating admin user: " . $conn->error;
}
?>