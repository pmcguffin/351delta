<?php
session_start();

// Ensure the user is an Alumni
if (!isset($_SESSION['Alumni_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an alumni
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard</title>
</head>
<body>
    <h2>Welcome, Alumni!</h2>
    <p>You're logged in as: <strong><?php echo $_SESSION['Alumni_Email']; ?></strong></p>

    <!-- Add Alumni specific content here -->
    <p>Here you can access alumni-related features.</p>

    <a href="logout.php">Logout</a> <!-- Logout option -->
</body>
</html>
