<?php
session_start();

// Ensure the user is an Admin
if (!isset($_SESSION['Admin_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}
?>