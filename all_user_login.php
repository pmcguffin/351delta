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

        .error {
            color: red;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <header>
        <h2>Login to Your Account</h2>
    </header>

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

            <label for="username">Username:</label>
            <input name="uname" id="username" type="text" required><br>

            <label for="password">Password:</label>
            <input name="pword" id="password" type="password" required><br>

            <button type="submit" name="submit">Submit</button>
        </form>

        <?php
        session_start();
        include('Connection.php');

        if (isset($_POST['submit'])) {
            $uname = trim($_POST['uname']);
            $pword = trim($_POST['pword']);
            $classification = $_POST['classification'];

            $table = "";
            $email_column = "";

            switch ($classification) {
                case "Admin":
                    $table = "admin_account";
                    $email_column = "Admin_Email";
                    break;
                case "Alumni":
                    $table = "alumni_account";
                    $email_column = "Alumni_Email";
                    break;
                case "Employer":
                    $table = "employers_account";
                    $email_column = "Employer_Email";
                    break;
                case "Professor":
                    $table = "professors_account";
                    $email_column = "Professor_Email";
                    break;
                case "Student":
                    $table = "student_account";
                    $email_column = "Student_Email";
                    break;
                default:
                    echo "<p class='error'>❌ Invalid classification.</p>";
                    exit();
            }

            $sql = "SELECT $email_column, Password_Hash FROM $table WHERE $email_column = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stored_hash = $row['Password_Hash'];

                if ($pword === $stored_hash) {  // Change to password_verify($pword, $stored_hash) if using hashed passwords
                    session_unset();  // Clear all existing session variables to avoid conflicts
                    $_SESSION[$email_column] = $row[$email_column];  // Set the correct session variable for the user type
                    header("Location: main.php");
                    exit();
                } else {
                    echo "<p class='error'>❌ Login failed. Incorrect password.</p>";
                }
            } else {
                echo "<p class='error'>❌ Login failed. User not found.</p>";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2025 User Login</p>
    </footer>

</body>
</html>
