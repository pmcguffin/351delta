<?php
session_start();
if (!isset($_SESSION['Employer_Email']) && 
!isset($_SESSION['Professor_Email']) && 
!isset($_SESSION['Alumni_Email'])) {
    header("Location: login.php"); // Redirect to login page if not an employer
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_description = $_POST['job_description'];
    $company_name = $_POST['company_name'];
    $major = $_POST['major'];
    
    // Get the highest current job_id
    $max_id_query = "SELECT MAX(job_id) as max_id FROM jobs";
    $max_id_result = $conn->query($max_id_query);
    $max_id_row = $max_id_result->fetch_assoc();
    $new_job_id = ($max_id_row['max_id'] !== null) ? $max_id_row['max_id'] + 1 : 1;
    
    // Insert new job with alumni_email if alumni is logged in
    if (isset($_SESSION['Alumni_Email'])) {
        $alumni_email = $_SESSION['Alumni_Email'];
        $sql = "INSERT INTO jobs (job_id, job_description, company_name, major, alumni_email) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $new_job_id, $job_description, $company_name, $major, $alumni_email);
    } else {
        $sql = "INSERT INTO jobs (job_id, job_description, company_name, major) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $new_job_id, $job_description, $company_name, $major);
    }

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
              </tr>";
              
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["job_description"] . "</td>";
            echo "<td>" . $row["company_name"] . "</td>";
            echo "<td>" . $row["major"] . "</td>";
            echo "<td>" . ($row["alumni_email"] ?? 'N/A') . "</td>";
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
    
    <?php
    $conn->close();
    ?>
</body>
</html>
