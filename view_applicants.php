<?php
session_start();
if (!isset($_SESSION['Employer_Email']) && 
    !isset($_SESSION['Professor_Email']) && 
    !isset($_SESSION['Alumni_Email']) && 
    !isset($_SESSION['Admin_Email'])) {
    header("Location: login.php");
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

// Get current user's email
$current_user_email = $_SESSION['Employer_Email'] ?? 
                     $_SESSION['Professor_Email'] ?? 
                     $_SESSION['Alumni_Email'] ?? 
                     $_SESSION['Admin_Email'] ?? '';

// Check if job_id is provided
if (!isset($_GET['job_id'])) {
    header("Location: jobs_menu.php");
    exit();
}

$job_id = $_GET['job_id'];

// Verify the job belongs to the current user
$job_sql = "SELECT poster_email FROM jobs WHERE job_id = ? AND deleted = 0";
$job_stmt = $conn->prepare($job_sql);
$job_stmt->bind_param("i", $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();

if ($job_result->num_rows == 0 || $job_result->fetch_assoc()['poster_email'] !== $current_user_email) {
    header("Location: jobs_menu.php");
    exit();
}
$job_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants</title>
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
    </style>
</head>
<body>
    <h1>Applicants for Job ID: <?php echo $job_id; ?></h1>
    
    <?php
    // Query to fetch applicants
    $sql = "SELECT application_id, student_email FROM applications WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Application ID</th>
                <th>Student Email</th>
              </tr>";
              
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["application_id"] . "</td>";
            echo "<td>" . $row["student_email"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total applicants: " . $result->num_rows . "</p>";
    } else {
        echo "<p>No applicants found for this job.</p>";
    }
    
    $stmt->close();
    ?>
    
    <p><a href="jobs_menu.php">Return to Job Listings</a></p>
    
    <?php
    $conn->close();
    ?>
</body>
</html>