<?php
session_start();
include('home_icon2.php'); 

// Ensure the user is an Admin
if (!isset($_SESSION["Admin_Email"])) {
    die("Access denied. Admins only.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "351delta";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";
$error_message = "";

// Verify Alumni or Employer Account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["verify"])) {
    $email = $_POST["email"];
    $userType = $_POST["user_type"];
    $table = $userType === "Alumni" ? "Alumni_Account" : "Employers_Account";
    $email_column = $userType === "Alumni" ? "Alumni_Email" : "Employer_Email";

    $update_sql = "UPDATE $table SET verified = 1 WHERE $email_column = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        $success_message = "✅ Account verified successfully!";
    } else {
        $error_message = "❌ Error verifying account.";
    }
}

// Fetch unverified accounts
$pending_alumni = $conn->query("SELECT * FROM Alumni_Account WHERE verified = 0");
$pending_employers = $conn->query("SELECT * FROM Employers_Account WHERE verified = 0");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Verification</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #041E42; /* CNU Navy Blue */
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 20px;
            background-color: white;
            color: #041E42; /* Navy Blue Text */
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        h2 {
            text-align: center;
            color: #041E42;
        }

        .top-bar {
            text-align: center;
            margin-top: 20px;
        }
		
        .left-bar {
            text-align: left;
            margin-top: 20px;
        }

        .dashboard-button {
            background-color: #0077C8; /* Light Blue */
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }

        .dashboard-button:hover {
            background-color: #005A9C; /* Darker Blue */
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #ccffcc;
            color: #006600;
        }

        .error {
            background-color: #ffcccc;
            color: #d8000c;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: #f1f1f1;
            color: #041E42;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            background-color: #005A9C; /* Light Blue */
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background-color: #005A9C; /* Darker Blue */
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>



    <div class="container">
        <h2>Admin Verification</h2>
		

        <!-- Success & Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h2>Pending Alumni Accounts</h2>
        <ul>
            <?php while ($row = $pending_alumni->fetch_assoc()): ?>
                <li>
                    <span><?php echo htmlspecialchars($row["Name"]) . " - " . htmlspecialchars($row["Alumni_Email"]); ?></span>
                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['Alumni_Email']); ?>">
                        <input type="hidden" name="user_type" value="Alumni">
                        <button type="submit" name="verify">Verify</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>

        <h2>Pending Employer Accounts</h2>
        <ul>
            <?php while ($row = $pending_employers->fetch_assoc()): ?>
                <li>
                    <span><?php echo htmlspecialchars($row["Name"]) . " - " . htmlspecialchars($row["Employer_Email"]); ?></span>
                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['Employer_Email']); ?>">
                        <input type="hidden" name="user_type" value="Employer">
                        <button type="submit" name="verify">Verify</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
			    <div class="left-bar">
        <!-- <a href="admin_dashboard.php" class="dashboard-button">⬅ Back to Admin Dashboard</a> -->
    </div>
    </div>
	

</body>
</html>
