<?php
session_start();
if (isset($_SESSION['Employer_Email'])) {
    header("Location: employer_dashboard.php");
    exit;
} 
if (isset($_SESSION['Professor_Email'])) {  
    header("Location: professor_dashboard.php");  
    exit;
} 
if (isset($_SESSION['Alumni_Email'])) {  
    header("Location: alumni_dashboard.php"); 
    exit;
}
if (isset($_SESSION['Admin_Email'])) {  
    header("Location: admin_dashboard.php"); 
    exit;
}
if (isset($_SESSION['Student_Email'])) {  
    header("Location: Student_dashboard.php"); 
    exit;
} 
?>