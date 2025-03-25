<?php
session_start(); // Start the session

// Ensure the user is an Professor
if (!isset($_SESSION['Professor_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an professor
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link rel="stylesheet" href="css_style.css"> <!-- Link to the external CSS file -->
</head>
<body>
    <header>
        <h1>Professor Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, Professor!</h2>
            <p>You are logged in as: <strong><?php echo $_SESSION['Professor_Email']; ?></strong></p>

            <p>Here you can manage all professor features:</p>

            <!-- Buttons for different professor features -->
            <a href="account_settings.php" class="btn">Account Settings</a><br>
            <a href="messages.php" class="btn">Messages</a><br>
            <a href="jobs_menu.php" class="btn">Job Posts</a><br>
            <!-- <a href="students.php" class="btn">Students</a><br><br> -->

            <a href="logout.php" class="btn logout-btn">Logout</a> <!-- Logout option -->
        </div>
    </div>
</body>
</html>
