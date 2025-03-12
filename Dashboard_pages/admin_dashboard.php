<?php
session_start();

// Ensure the user is an Admin
if (!isset($_SESSION['Admin_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, Admin!</h2>
    <p>You're logged in as: <strong><?php echo $_SESSION['Admin_Email']; ?></strong></p>

    <!-- Add Admin specific content here -->
    <p>Here you can manage all admin features.</p>

    <a href="logout.php">Logout</a> <!-- Logout option -->
</body>
</html>
