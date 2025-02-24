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

// SQL query to fetch data
$sql = "SELECT Email, Name, Phone_Number, Major, Company_Name, Graduation_Year, User_Type FROM Pending_Accounts"; // Adjust table/column names
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
						<form method='POST' action='process_action.php'>
						<input type='hidden' name='email' value='" . htmlspecialchars($row['Email'], ENT_QUOTES, 'UTF-8') . "'>
						<button type='submit' name='action' value='approve' class='approve'>Approve</button>
						<button type='submit' name='action' value='deny' class='deny'>Deny</button>
					</form>
					</td>



                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No records found</td></tr>";
    }
    ?>

</table>


</body>
</html>

<?php
$conn->close();
?>
