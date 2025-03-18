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
    <title>Professor Dashboard</title>
    <link rel="stylesheet" href="css_style.css"> <!-- Link to the external CSS file -->
</head>
<body>
    <header>
        <h1>Student Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <h2>Welcome, Student!</h2>
            <p>You are logged in as: <strong><?php echo $_SESSION['Student_Email']; ?></strong></p>

            <p>Here you can manage all student features:</p>

            <!-- Buttons for different professor features -->
            <a href="account_settings.php" class="btn">Account Settings</a><br>
            <a href="messages.php" class="btn">Messages</a><br>
            <a href="student_jobs_menu.php" class="btn">Job Posts</a><br>
            <!-- <a href="students.php" class="btn">Students</a><br><br> -->

            <a href="logout.php" class="btn logout-btn">Logout</a> <!-- Logout option -->
        </div>
    </div>
</body>
</html>
