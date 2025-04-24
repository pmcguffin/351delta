<?php
// Josh's code
session_start();

// Check if any user type is logged in
if (!isset($_SESSION['Admin_Email']) && 
    !isset($_SESSION['Employer_Email']) && 
    !isset($_SESSION['Professor_Email']) && 
    !isset($_SESSION['Student_Email']) && 
    !isset($_SESSION['Alumni_Email'])) {
    
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}

// Redirect to the specific dashboard based on the session variable
if (isset($_SESSION['Admin_Email'])) {
    header("Location: admin_dashboard.php");
    exit();
} elseif (isset($_SESSION['Alumni_Email'])) {
    header("Location: alumni_dashboard.php");
    exit();
} elseif (isset($_SESSION['Employer_Email'])) {
    header("Location: employer_dashboard.php");
    exit();
} elseif (isset($_SESSION['Professor_Email'])) {
    header("Location: professor_dashboard.php");
    exit();
} elseif (isset($_SESSION['Student_Email'])) {
    header("Location: student_dashboard.php");
    exit();
}

?>
