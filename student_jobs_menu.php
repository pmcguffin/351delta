<?php
session_start();
if (!isset($_SESSION['Student_Email'])) {
    header("Location: login.php"); // Redirect to login page if not a student
    exit();
}

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

// Handle job application
if (isset($_GET['apply_job_id'])) {
    $job_id = $_GET['apply_job_id'];
    $student_email = $_SESSION['Student_Email'];
    
    // Get the highest current application_id
    $max_id_query = "SELECT MAX(application_id) as max_id FROM applications";
    $max_id_result = $conn->query($max_id_query);
    $max_id_row = $max_id_result->fetch_assoc();
    $new_application_id = ($max_id_row['max_id'] !== null) ? $max_id_row['max_id'] + 1 : 1;
    
    // Check if student has already applied
    $check_sql = "SELECT * FROM applications WHERE student_email = ? AND job_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $student_email, $job_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // Insert new application
        $sql = "INSERT INTO applications (application_id, student_email, job_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $new_application_id, $student_email, $job_id);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Application submitted successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error submitting application: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
    $check_stmt->close();
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
        .apply-btn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .apply-btn:hover {
            background-color: #45a049;
        }
        .applied-text {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Job Listings Dashboard</h1>
    
    <?php
    // Query to fetch all jobs
    $sql = "SELECT job_id, job_description, company_name, major, alumni_email FROM jobs WHERE deleted = 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Description</th>
                <th>Company</th>
                <th>Major</th>
                <th>Alumni Email</th>
                <th>Action</th>
              </tr>";
              
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["job_description"] . "</td>";
            echo "<td>" . $row["company_name"] . "</td>";
            echo "<td>" . $row["major"] . "</td>";
            echo "<td>" . ($row["alumni_email"] ?? 'N/A') . "</td>";
            
            // Check if student has applied
            $student_email = $_SESSION['Student_Email'];
            $check_sql = "SELECT * FROM applications WHERE student_email = ? AND job_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $student_email, $row["job_id"]);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            echo "<td>";
            if ($check_result->num_rows > 0) {
                echo "<span class='applied-text'>Applied</span>";
            } else {
                echo "<a href='?apply_job_id=" . $row["job_id"] . "' class='apply-btn'>Apply</a>";
            }
            echo "</td>";
            
            $check_stmt->close();
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total jobs: " . $result->num_rows . "</p>";
    } else {
        echo "<p>No jobs found in the database.</p>";
    }
    ?>
    
    <p><a href="student_dashboard.php">Return to Dashboard</a></p>
    
    <?php
    $conn->close();
    ?>
</body>
</html>