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
    <link rel="stylesheet" href="css_style.css"> <!-- Link to the external CSS file -->
</head>
<body>

<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, Admin!</h2>
            <p>You're logged in as: <strong><?php echo $_SESSION['Admin_Email']; ?></strong></p>

            <p>Here you can manage all admin features:</p>

            <a href="account_settings.php" class="btn">Account Settings</a><br>
            <a href="message_ui.php" class="btn">Messages</a><br>
            <a href="jobs_menu.php" class="btn">Job Posts</a><br>
			<a href="admin_verify.php" class="btn">Approve new accounts</a><br>
            <a href="admin_jobs_menu.php" class="btn">Manage Job Posts</a><br>
            <!-- <a href="events.php" class="btn">Events</a><br><br> -->
	    <a href="findotherspt2.php" class="btn">Find Others</a><br>

            <a href="logout.php" class="btn logout-btn">Logout</a> <!-- Logout option -->
        </div>
    </div>
</body>
</html>
