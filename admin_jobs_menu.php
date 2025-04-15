<?php
session_start();
if (!isset($_SESSION['Admin_Email'])) {
    header("Location: login.php");
    exit();
}
?>

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

// Handle confirmed deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete']) && isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);
    $delete_sql = "UPDATE jobs SET deleted = 1 WHERE job_id = '$delete_id'";
    if ($conn->query($delete_sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings Dashboard</title>
    <!-- Link to external CSS file -->
    <link rel="stylesheet" href="css_style.css">
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
        .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
        .confirm-box {
            background-color: #fff;
            border: 2px solid #ff4444;
            padding: 15px;
            margin: 20px 0;
        }
        .confirm-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 5px;
            cursor: pointer;
        }
        .confirm-btn:hover {
            background-color: #cc0000;
        }
        .cancel-btn {
            background-color: #666;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 5px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Job Listings Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-box">
            <?php
            // Show confirmation box if delete_id is set in POST but not confirmed yet
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && !isset($_POST['confirm_delete'])) {
                $confirm_id = $conn->real_escape_string($_POST['delete_id']);
                $sql = "SELECT job_description, company_name FROM jobs WHERE job_id = '$confirm_id'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $job = $result->fetch_assoc();
                    echo "<div class='confirm-box'>";
                    echo "<p>Are you sure you want to delete this job?</p>";
                    echo "<p>Description: " . $job['job_description'] . "</p>";
                    echo "<p>Company: " . $job['company_name'] . "</p>";
                    echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>";
                    echo "<input type='hidden' name='delete_id' value='$confirm_id'>";
                    echo "<input type='hidden' name='confirm_delete' value='1'>";
                    echo "<button type='submit' class='confirm-btn btn'>Yes, Delete</button>";
                    echo "<a href='" . $_SERVER['PHP_SELF'] . "'><button type='button' class='cancel-btn btn'>Cancel</button></a>";
                    echo "</form>";
                    echo "</div>";
                }
            }
            ?>
            
            <?php
            // Query to fetch all jobs
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
                    echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>";
                    echo "<input type='hidden' name='delete_id' value='" . $row["job_id"] . "'>";
                    echo "<button type='submit' class='delete-btn btn'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p>Total jobs: " . $result->num_rows . "</p>";
            } else {
                echo "<p>No jobs found in the database.</p>";
            }
            ?>
            
            <p><a href="admin_dashboard.php" class="btn">Return to Dashboard</a></p>
        </div>
    </div>
    
    <?php
    $conn->close();
    ?>
</body>
</html>