<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "";
$database = "351delta";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_description = $_POST['job_description'];
    $company_name = $_POST['company_name'];
    $major = $_POST['major'];
    
    // Get the highest current job_id
    $max_id_query = "SELECT MAX(job_id) as max_id FROM jobs";
    $max_id_result = $conn->query($max_id_query);
    $max_id_row = $max_id_result->fetch_assoc();
    $new_job_id = $max_id_row['max_id'] + 1;
    
    // Insert new job with null alumni_email
    $sql = "INSERT INTO jobs (job_id, job_description, company_name, major, alumni_email) 
            VALUES (?, ?, ?, ?, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $new_job_id, $job_description, $company_name, $major);
    
    if ($stmt->execute()) {
        echo "<p>Job posting created successfully!</p>";
    } else {
        echo "<p>Error creating job posting: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .job-form {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .job-form label {
            display: block;
            margin: 10px 0 5px;
        }
        .job-form input, .job-form textarea {
            width: 100%;
            max-width: 400px;
            padding: