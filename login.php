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

    // Define user types and corresponding tables
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
            header("Location: login.php?error=not_verified");
            exit();
        }

        // Check password
        if (password_verify($pword, $stored_hash)) {
            session_unset();
           $_SESSION[$email_column] = $row[$email_column];
           $_SESSION['user_email'] = $row[$email_column];
           $_SESSION['user_type'] = $classification;  // This will hold the classification (e.g., Admin, Alumni, etc.)

            header("Location: main.php");
            exit();
        } else {
            header("Location: login.php?error=incorrect_password");
            exit();
        }
    } else {
        header("Location: login.php?error=user_not_found");
        exit();
    }

    $stmt->close();
    $conn->close();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

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
            max-width: 400px;
            margin: 60px auto;
            padding: 25px;
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

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #041E42;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        .error-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .error-message {
            background-color: #ffcccc;
            color: #d8000c;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            display: inline-block;
        }

        button {
            background-color: #0077C8; /* Light Blue */
            color: white;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }

        button:hover {
            background-color: #005A9C; /* Darker Blue */
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #041E42;
        }

        .register-link a {
            color: #0077C8;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2>Login to Your Account</h2>

        <!-- Display Error Messages -->
        <?php
        if (isset($_GET['error'])) {
            echo "<div class='error-container'><div class='error-message'>";
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
            echo "</div></div>";
        }
        ?>

        <form action="" method="post">
            <label>Choose your classification:</label>
            <div class="radio-group">
                <label><input type="radio" name="classification" value="Admin" required> Admin</label>
                <label><input type="radio" name="classification" value="Alumni"> Alumni</label>
                <label><input type="radio" name="classification" value="Employer"> Employer</label>
                <label><input type="radio" name="classification" value="Professor"> Professor</label>
                <label><input type="radio" name="classification" value="Student"> Student</label>
            </div>

            <label for="username">Email:</label>
            <input name="uname" id="username" type="text" required placeholder="example@cnu.edu">

            <label for="password">Password:</label>
            <input name="pword" id="password" type="password" required placeholder="*********">

            <button type="submit" name="submit">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? 
            <a href="registration.php">Register</a>
        </p>
    </div>

</body>
</html>