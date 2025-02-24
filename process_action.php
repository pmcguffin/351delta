<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "351delta";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email and action from URL
if (isset($_GET['email']) && isset($_GET['action'])) {
    $email = $conn->real_escape_string($_GET['email']);
	$type = $_GET['User_Type'];
    $action = $_GET['action'];

    if ($action === "approve") {
        // Move user to approved accounts
        if ($type === 0) {
			$sql = "INSERT INTO Alumni_Account (Alumni_Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash)
                SELECT Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash FROM Pending_Accounts WHERE Email = '$email'";
		}
		else {
				$sql = "INSERT INTO Employers_Account (Employer_Email, Name, Phone_Number, Major, Company_Name, Password_Hash)
                SELECT Email, Name, Phone_Number, Company_Name, Password_Hash FROM Pending_Accounts WHERE Email = '$email'";
		}

        if ($conn->query($sql) === TRUE) {
            // Delete from pending
            $conn->query("DELETE FROM Pending_Accounts WHERE Email = '$email'");
            echo "User approved successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif ($action === "deny") {
        // Simply delete the user from pending
        $sql = "DELETE FROM Pending_Accounts WHERE Email = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo "User denied successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Redirect back to pending accounts page
header("Location: acount_approval.php");
exit();

$conn->close();
?>
