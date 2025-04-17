<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "351delta";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = trim($_POST["confirmation_code"]);

    if ($entered_code == $_SESSION['confirmation_code']) {
        // Insert user into the appropriate table
        $email = $_SESSION['email'];
        $name = $_SESSION['name'];
        $phone = $_SESSION['phone'];
        $hashed_password = $_SESSION['hashed_password'];
        $userType = $_SESSION['userType'];

        switch ($userType) {
            case "Alumni":
                $sql = "INSERT INTO Alumni_Account (Alumni_Email, Name, Phone_Number, Password_Hash, verified) 
                        VALUES (?, ?, ?, ?, 0)";
                break;
            case "Professor":
                $sql = "INSERT INTO Professors_Account (Professor_Email, Name, Phone_Number, Password_Hash) 
                        VALUES (?, ?, ?, ?)";
                break;
            case "Student":
                $sql = "INSERT INTO Student_Account (Student_Email, Name, Phone_Number, Password_Hash) 
                        VALUES (?, ?, ?, ?)";
                break;
            case "Employer":
                $sql = "INSERT INTO Employers_Account (Employer_Email, Name, Phone_Number, Password_Hash, verified) 
                        VALUES (?, ?, ?, ?, 0)";
                break;
            case "Admin":
                $sql = "INSERT INTO Admin_Account (Admin_Email, Name, Phone_Number, Password_Hash) 
                        VALUES (?, ?, ?, ?)";
                break;
            default:
                $error = "❌ Invalid user type.";
        }

        if (empty($error)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $name, $phone, $hashed_password);

            if ($stmt->execute()) {
                session_destroy(); // Clear session
                header("Location: login.php"); // Redirect to login
                exit();
            } else {
                $error = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "❌ Invalid confirmation code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            color: #041E42; /* Navy Blue Text */
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #041E42;
        }

        label {
            display: block;
            margin: 10px 0;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #041E42;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        button {
            background-color: #0077C8; /* CNU Light Blue */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        button:hover {
            background-color: #005A9C; /* Darker Blue */
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Email Verification</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Enter Confirmation Code:</label>
            <input type="text" name="confirmation_code" required>
            <button type="submit">Verify</button>
        </form>
    </div>

</body>
</html>
