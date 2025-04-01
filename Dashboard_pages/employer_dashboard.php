<?php
session_start();

// Ensure the user is an Employer
if (!isset($_SESSION['Employer_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an employer
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
        <h1>Employer Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, Employer!</h2>
            <p>You're logged in as: <strong><?php echo $_SESSION['Employer_Email']; ?></strong></p>

            <p>Here you can manage all employer features:</p>

            <!-- Buttons for different employer features -->
            <a href="account_settings.php" class="btn">Account Settings</a><br>
            <a href="messages.php" class="btn">Messages</a><br>
            <a href="jobs_menu.php" class="btn">Job Posts</a><br>
            <!-- <a href="applicants.php" class="btn">Applicants</a><br><br> -->
            <a href="findotherspt2.php" class="btn">Find Others</a><br>

            <a href="logout.php" class="btn logout-btn">Logout</a> <!-- Logout option -->
        </div>
    </div>
</body>
</html>
