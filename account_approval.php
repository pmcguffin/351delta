<?php
//session_start();

// Ensure the user is an Admin
//if (!isset($_SESSION['Admin_Email'])) {
    //header("Location: login.php"); // Redirect to login page if not an admin
    //exit();
//}

$servername = "localhost";
$username = "root";
$password = "";
$database = "351delta";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Get the User_Type_Value to determine where to insert
        $query = "SELECT User_Type FROM Pending_Accounts WHERE Email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userTypeValue = $row['User_Type'];

            if ($userTypeValue == 0) {
                $sql = "INSERT INTO Alumni_Account (Alumni_Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash)
                        SELECT Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash
                        FROM Pending_Accounts WHERE Email = '$email'";
						
				if ($conn->query($sql) === TRUE) {
                // Remove from pending accounts
                $conn->query("DELETE FROM Pending_Accounts WHERE Email = '$email'");
                echo "<script>alert('User approved successfully!'); window.location.reload();</script>";
            } else {
                echo "<script>alert('Error approving user: " . $conn->error . "');</script>";
            }
            } else {
                $sql = "INSERT INTO Employers_Account (Employer_Email, Name, Phone_Number, Company_Name, Password_Hash)
                        SELECT Email, Name, Phone_Number, Company_Name, Password_Hash
                        FROM Pending_Accounts WHERE Email = '$email'";
						
				if ($conn->query($sql) === TRUE) {
                // Remove from pending accounts
                $conn->query("DELETE FROM Pending_Accounts WHERE Email = '$email'");
                echo "<script>alert('User approved successfully!'); window.location.reload();</script>";
            } else {
                echo "<script>alert('Error approving user: " . $conn->error . "');</script>";
            }
            }

        }
    } elseif ($action == 'deny') {
        // Remove user from pending accounts
        if ($conn->query("DELETE FROM Pending_Accounts WHERE Email = '$email'") === TRUE) {
            echo "<script>alert('User denied successfully!'); window.location.href = window.location.pathname;</script>";
        } else {
            echo "<script>alert('Error denying user: " . $conn->error . "');</script>";
        }
    }
}

// Fetch pending accounts again after processing
$sql = "SELECT Email, Name, Phone_Number, Major, Company_Name, Graduation_Year, User_Type FROM Pending_Accounts";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users Table</title>
    <style>
        table { width: 50%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; }
        .approve { background-color: #4CAF50; color: white; }
        .deny { background-color: #f44336; color: white; }
    </style>
</head>
<body>

<h2>Pending Accounts</h2>

<table>
    <tr>
        <th>Email</th>
        <th>Name</th>
        <th>Phone_Number</th>
        <th>Major</th>
        <th>Company_Name</th>
        <th>Graduation_Year</th>
        <th>Account_Type</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Email']}</td>
                    <td>{$row['Name']}</td>
                    <td>{$row['Phone_Number']}</td>
                    <td>{$row['Major']}</td>
                    <td>{$row['Company_Name']}</td>
                    <td>{$row['Graduation_Year']}</td>
                    <td>{$row['User_Type']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='email' value='" . htmlspecialchars($row['Email'], ENT_QUOTES, 'UTF-8') . "'>
                            <button type='submit' name='action' value='approve' class='approve'>Approve</button>
                            <button type='submit' name='action' value='deny' class='deny'>Deny</button>
                        </form>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No records found</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
