<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #001f3d; /* Navy Blue */
            color: white;
            padding: 15px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }
        /* Add this to your CSS */
        
        input[type="text"], input[type="password"], input[type="radio"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #001f3d; /* Navy Blue */
            color: white;
            border: none;
            padding: 12px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005bb5; /* Lighter blue on hover */
        }

        .radio-label {
            margin: 10px 0;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #001f3d;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

		.error-container {
			display: flex;
			justify-content: center;  /* Centers horizontally */
			align-items: center;  /* Aligns towards the top */
			height: 20vh;  /* Reduces whitespace */
			flex-direction: column;
			margin-top: 20px; /* Adds a small gap from the top */
		}

		.error-message {
			background-color: #ffcccc;
			color: #d8000c;
			text-align: center;
			padding: 12px;
			font-size: 16px;
			font-weight: bold;
			border: 1px solid #d8000c;
			border-radius: 5px;
			width: 50%;
			max-width: 500px;
		}


    </style>
</head>
<body>

    <header>
        <h2>Login to Your Account</h2>
    </header>

<!-- Display Error Messages Here -->
<<?php
if (isset($_GET['error'])) {
    echo "<div class='error-container'>";
    echo "<div class='error-message'>";
    switch ($_GET['error']) {
        case 'not_verified':
            echo "❌ Your account has not been verified by an admin yet.";
            break;
        case 'incorrect_password':
            echo "❌ Incorrect password. Try again.";
            break;
        case 'user_not_found':
            echo "❌ No account found with that email.";
            break;
        case 'invalid_classification':
            echo "❌ Invalid classification selected.";
            break;
    }
    echo "</div>";
    echo "</div>";
}
?>


    <div class="container">
       <form action="" method="post">
    <p class="radio-label">Choose your classification:</p>
    <input type="radio" id="Admin" name="classification" value="Admin" required>
    <label for="Admin">Admin</label><br>
    <input type="radio" id="Alumni" name="classification" value="Alumni">
    <label for="Alumni">Alumni</label><br>
    <input type="radio" id="Employer" name="classification" value="Employer">
    <label for="Employer">Employer</label><br>
    <input type="radio" id="Professor" name="classification" value="Professor">
    <label for="Professor">Professor</label><br>
    <input type="radio" id="Student" name="classification" value="Student">
    <label for="Student">Student</label><br><br>

    <label for="username">Email:</label>
    <input name="uname" id="username" type="text" required><br>

    <label for="password">Password:</label>
    <input name="pword" id="password" type="password" required><br>

    <button type="submit" name="submit">Submit</button>
</form>




    <?php
ob_start();  // Prevents unexpected output
session_start();
session_regenerate_id(true);
include('Connection.php');

if (isset($_POST['submit'])) {
    if (!$conn) {
        die("<p class='error'>❌ Database connection failed.</p>");
    }

    // Sanitize input
    $uname = htmlspecialchars(trim($_POST['uname']));
    $pword = trim($_POST['pword']);
    $classification = $_POST['classification'] ?? '';

    // Define user types
    $tables = [
        "Admin" => ["admin_account", "Admin_Email"],
        "Alumni" => ["alumni_account", "Alumni_Email"],
        "Employer" => ["employers_account", "Employer_Email"],
        "Professor" => ["professors_account", "Professor_Email"],
        "Student" => ["student_account", "Student_Email"],
    ];

    if (!isset($tables[$classification])) {
        header("Location: login.php?error=invalid_classification");
        exit();
    }

    list($table, $email_column) = $tables[$classification];

// Modify SQL to check the "verified" status for Alumni and Employers
$verified_check = ($classification === "Alumni" || $classification === "Employer") ? ", verified" : "";

$sql = "SELECT $email_column, Password_Hash $verified_check FROM $table WHERE $email_column = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_hash = $row['Password_Hash'];

    // Check verification status for Alumni and Employers
    if (($classification === "Alumni" || $classification === "Employer") && isset($row['verified']) && $row['verified'] == 0) {
        header("Location: all_user_login.php?error=not_verified");
        exit();
    }

    // Check password
    if (password_verify($pword, $stored_hash)) {
        session_unset();
        $_SESSION[$email_column] = $row[$email_column];
        header("Location: main.php");
        exit();
    } else {
        header("Location: all_user_login.php?error=incorrect_password");
        exit();
    }
} else {
    header("Location: all_user_login.php?error=user_not_found");
    exit();
}

$stmt->close();
$conn->close();

}
ob_end_flush();
?>

<p style="margin-top: 15px; font-size: 14px; color: #041E42;">
				Don't have an account? 
				<a href="registration.php" style="color: #0077C8; text-decoration: none; font-weight: bold;">
				Register
				</a>
			</p>
</body>
</html>
