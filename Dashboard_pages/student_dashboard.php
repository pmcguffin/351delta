<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['Student_Email'])) {
    header("Location: main.php"); // Redirect to the login page if not authenticated
    exit();
}

// Student is logged in, proceed to the student's dashboard or landing page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <h2>Welcome, Student!</h2>
    <p>You are logged in as: <strong><?php echo $_SESSION['Student_Email']; ?></strong></p>
    <p>Welcome to your dashboard!</p>
    <a href="logout.php">Logout</a> <!-- Logout option -->
</body>
</html>
