<?php
session_start();

// Check if any user type is logged in
if (!isset($_SESSION['Admin_Email']) && 
    !isset($_SESSION['Employer_Email']) && 
    !isset($_SESSION['Professor_Email']) && 
    !isset($_SESSION['Student_Email']) && 
    !isset($_SESSION['Alumni_Email'])) {
    
    header("Location: main.php"); // Redirect to login page if not authenticated
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome!</h2>

    <p>You are logged in as: 
        <strong>
            <?php 
                if (isset($_SESSION['Admin_Email'])) echo $_SESSION['Admin_Email'];
                elseif (isset($_SESSION['Employer_Email'])) echo $_SESSION['Employer_Email'];
                elseif (isset($_SESSION['Professor_Email'])) echo $_SESSION['Professor_Email'];
                elseif (isset($_SESSION['Student_Email'])) echo $_SESSION['Student_Email'];
                elseif (isset($_SESSION['Alumni_Email'])) echo $_SESSION['Alumni_Email'];
            ?>
        </strong>
    </p>

    <a href="logout.php">Logout</a> <!-- Logout option -->
</body>
</html>
