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
    <title>Professor Dashboard</title>
    <link rel="stylesheet" href="css_style.css"> <!-- Link to the external CSS file -->
</head>
<body>

<body>
    <header>
        <h1>Alumni Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, Alumni!</h2>
            <p>You're logged in as: <strong><?php echo $_SESSION['Alumni_Email']; ?></strong></p>

            <p>Here you can access alumni-related features:</p>

            <a href="account_settings.php" class="btn">Account Settings</a><br>
            <a href="message.php" class="btn">Messages</a><br>
            <a href="posts.php" class="btn">Posts</a><br>
            <a href="events.php" class="btn">Events</a><br><br>

            <a href="logout.php" class="btn logout-btn">Logout</a> <!-- Logout option -->
        </div>
    </div>
</body>
</html>
