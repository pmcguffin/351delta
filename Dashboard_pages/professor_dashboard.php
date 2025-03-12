<?php
session_start();

// Check if the professor is logged in
if (!isset($_SESSION['Professor_Email'])) {
    header("Location: main.php"); // Redirect to the login page if not authenticated
    exit();
}

// Professor is logged in, proceed to the professor's dashboard or landing page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
</head>
<body>
    <h2>Welcome, Professor!</h2>
    <p>You are logged in as: <strong><?php echo $_SESSION['Professor_Email']; ?></strong></p>
    <p>Welcome to your dashboard!</p>
    <a href="logout.php">Logout</a> <!-- Logout option -->
</body>
</html>
