<?php
session_start();
if (!isset($_SESSION['Alumni_Email'] {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$username = $_SESSION['username'];
}

if (!isset($_SESSION['Employer_Email'] {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$username = $_SESSION['username'];
}

if (!isset($_SESSION['Professor_Email'] {
	$description = $_POST['Job_Description'];
	$company = $_POST['Company_Name'];
	$major = $_POST['Major'];
	$username = $_SESSION['Professor_Email'];
}

// Save to database (not implemented)
$title = $_POST['title'];
$description = $_POST['description'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// TODO: Insert into DB
// e.g. INSERT INTO job_posts (title, description, posted_by, user_type) VALUES (...)

header("Location: jobs_menu.php");
?>