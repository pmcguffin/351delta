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

// Handle job deletion
if (isset($_GET['delete_job_id'])) {
    $job_id = $_GET['delete_job_id'];
    
    $sql = "SELECT poster_email FROM jobs WHERE job_id = ? AND deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['poster_email'] === $current_user_email) {
            $delete_sql = "UPDATE jobs SET deleted = 1 WHERE job_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $job_id);
            
            if ($delete_stmt->execute()) {
                echo "<p style='color: green;'>Job deleted successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error deleting job: " . $conn->error . "</p>";
            }
            $delete_stmt->close();
        }
    }
    $stmt->close();
}

// Handle job creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_description = $_POST['job_description'];
    $company_name = $_POST['company_name'];
    $major = $_POST['major'];
    
    $max_id_query = "SELECT MAX(job_id) as max_id FROM jobs";
    $max_id_result = $conn->query($max_id_query);
    $max_id_row = $max_id_result->fetch_assoc();
    $new_job_id = ($max_id_row['max_id'] !== null) ? $max_id_row['max_id'] + 1 : 1;
    
    $sql = "INSERT INTO jobs (job_id, job_description, company_name, major, poster_email) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $new_job_id, $job_description, $company_name, $major, $current_user_email);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Job posting created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating job posting: " . $conn->error . "</p>";
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
            padding: 5px;
        }
        .job-form button {
            margin-top: 10px;
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .job-form button:hover {
            background-color: #45a049;
        }
        .delete-link {
            color: red;
            text-decoration: none;
        }
        .delete-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Job Listings Dashboard</h1>
    
    <?php
    // Display jobs
    $sql = "SELECT job_id, job_description, company_name, major, poster_email FROM jobs WHERE deleted = 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Description</th>
                <th>Company</th>
                <th>Major</th>
                <th>Email</th>
                <th>Action</th>
              </tr>";
              
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["job_description"] . "</td>";
            echo "<td>" . $row["company_name"] . "</td>";
            echo "<td>" . $row["major"] . "</td>";
            echo "<td>" . ($row["poster_email"] ?? 'N/A') . "</td>";
            
            echo "<td>";
            if ($row["poster_email"] === $current_user_email) {
                echo "<a href='?delete_job_id=" . $row["job_id"] . "' 
                       class='delete-link' 
                       onclick='return confirm(\"Are you sure you want to delete this job?\")'>Delete</a>";
            }
            echo "</td>";
            
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total jobs: " . $result->num_rows . "</p>";
    } else {
        echo "<p>No jobs found in the database.</p>";
    }
    ?>
    
    <div class="job-form">
        <h2>Create New Job Posting</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="job_description">Job Description:</label>
            <textarea name="job_description" id="job_description" required></textarea>
            
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" id="company_name" required>
            
            <label for="major">Major:</label>
            <input type="text" name="major" id="major" required>
            
            <button type="submit">Create Job</button>
        </form>
    </div>
    
    <p><a href="return_to_dashboard.php">Return to Dashboard</a></p>
    
    <?php
    $conn->close();
    ?>
</body>
</html>