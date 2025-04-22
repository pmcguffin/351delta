<?php
session_start();
include('home_icon2.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "351delta";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$email = $name = $phone = $password = $major = $major2 = $minor = $grad_year = $grad_year2 = $company = "";
$userType = "";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["user_type"] ?? "";
    $email = trim($_POST["email"]);
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($password !== $confirm_password) {
        $error = "❌ Error: Passwords do not match.";
    }

    // Generate a random 6-digit confirmation code
    $confirmation_code = mt_rand(100000, 999999);
    $_SESSION['confirmation_code'] = $confirmation_code;
    $_SESSION['email'] = $email;
    $_SESSION['hashed_password'] = $hashed_password;
    $_SESSION['name'] = $name;
    $_SESSION['phone'] = $phone;
    $_SESSION['userType'] = $userType;

    // Ensure Students & Professors Use a CNU Email
    if (($userType === "Student" || $userType === "Professor") && !preg_match('/@cnu\.edu$/', $email)) {
        $error = "❌ Students and Professors must use a CNU email (@cnu.edu).";
    }

    // Prevent Duplicate Emails Across All Tables
    if (empty($error)) {
        $tables = [
            "Alumni_Account" => "Alumni_Email",
            "Professors_Account" => "Professor_Email",
            "Student_Account" => "Student_Email",
            "Employers_Account" => "Employer_Email",
            "Admin_Account" => "Admin_Email"
        ];

        foreach ($tables as $table => $email_column) {
            $check_sql = "SELECT * FROM $table WHERE $email_column = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "❌ Error: This email is already registered.";
                break;
            }
        }
    }

    // If no errors, send the confirmation email
    if (empty($error)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'noreplycnusec@gmail.com'; // Your sender email
            $mail->Password = 'psqjptsueffiznxe'; // Use an app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('noreplycnusec@gmail.com', 'CNU Registration');
            $mail->addAddress($email); // User's email

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification Code';
            $mail->Body = "Hello $name,<br><br>Your email verification code is: <b>$confirmation_code</b><br><br>Enter this code to complete your registration.";

            $mail->send();
            header("Location: verify_email.php"); // Redirect to verification page
            exit();
        } catch (Exception $e) {
            $error = "❌ Email could not be sent. Error: " . $mail->ErrorInfo;
        }
    }

}


    if (!empty($_POST["major"])) {
        $major = trim($_POST["major"]);
    }
	
	    if (!empty($_POST["major2"])) {
        $major2 = trim($_POST["major2"]);
    }
	
    if (!empty($_POST["minor"])) {
        $minor = trim($_POST["minor"]);
    }
    if (!empty($_POST["graduation_year"])) {
        $grad_year = trim($_POST["graduation_year"]);
		echo "!!!!!";
    }
	
	    if (!empty($_POST["graduation_year2"])) {
        $grad_year2 = trim($_POST["graduation_year2"]);
		echo "!!!!!";
    }
	
	
    if (!empty($_POST["company"])) {
        $company = trim($_POST["company"]);
    }

    // Ensure Students & Professors Use a CNU Email
    if (($userType === "Student" || $userType === "Professor") && !preg_match('/@cnu\.edu$/', $email)) {
        $error = "❌ Students and Professors must use a CNU email (@cnu.edu).";
    }

    // Prevent Duplicate Emails Across All Tables
    if (empty($error)) {
        $tables = [
            "Alumni_Account" => "Alumni_Email",
            "Professors_Account" => "Professor_Email",
            "Student_Account" => "Student_Email",
            "Employers_Account" => "Employer_Email",
            "Admin_Account" => "Admin_Email"
        ];

        foreach ($tables as $table => $email_column) {
            $check_sql = "SELECT * FROM $table WHERE $email_column = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "❌ Error: This email is already registered.";
                break;
            }
        }
    }

    // If no errors, insert the user into the database
    if (empty($error)) {
        switch ($userType) {
            case "Alumni":
                if (empty($grad_year2)) {
                  $error = "❌ Graduation year is required for Alumni.";
                   break;
               }
				
                $sql = "INSERT INTO Alumni_Account (Alumni_Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash, verified) 
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssis", $email, $name, $phone, $major2, $grad_year2, $hashed_password);
                break;

            case "Professor":
                $sql = "INSERT INTO Professors_Account (Professor_Email, Name, Phone_Number, Password_Hash) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $email, $name, $phone, $hashed_password);
                break;

            case "Student":
                if (empty($grad_year)) {
                    $error = "❌ Graduation year is required for Students.";
                    break;
                }
                $sql = "INSERT INTO Student_Account (Student_Email, Name, Phone_Number, Minor, Major, Graduation_Year, Password_Hash) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $email, $name, $phone, $minor, $major, $grad_year, $hashed_password);
                break;

            case "Employer":
                $sql = "INSERT INTO Employers_Account (Employer_Email, Name, Phone_Number, Company_Name, Password_Hash, verified) 
                        VALUES (?, ?, ?, ?, ?, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $email, $name, $phone, $company, $hashed_password);
                break;

            case "Admin":
                $sql = "INSERT INTO Admin_Account (Admin_Email, Name, Phone_Number, Password_Hash) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $email, $name, $phone, $hashed_password);
                break;


        }

        if (empty($error) && $stmt->execute()) {
            $success = "✅ Registration successful! You may now log in.";
        } elseif (empty($error)) {
            $error = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #041E42;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 10px 0;
        }

        .hidden {
            display: none;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        input[type="submit"] {
            background-color: #0077C8; /* CNU Light Blue */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: #005A9C; /* Darker Blue */
        }
    </style>

    <script>
        function updateForm() {
            let userType = document.querySelector('input[name="user_type"]:checked').value;
            document.getElementById("studentFields").style.display = userType === "Student" ? "block" : "none";
            document.getElementById("alumniFields").style.display = userType === "Alumni" ? "block" : "none";
            document.getElementById("employerFields").style.display = userType === "Employer" ? "block" : "none";
        }
		
        function validatePasswords() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let errorText = document.getElementById("passwordError");

            if (password !== confirmPassword) {
                errorText.style.display = "block";
                return false;
            } else {
                errorText.style.display = "none";
                return true;
            }
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>Register</h2>

        <!-- Display Error or Success Messages -->
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
		<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($success)): ?>
			<p class="success"><?php echo $success; ?></p>
		<?php endif; ?>

        <form method="post" onsubmit="return validatePasswords();">
            <label>Email: <input type="email" name="email" required placeholder="example@cnu.edu"></label>
            <label>Name: <input type="text" name="name" required placeholder="John Doe"></label>
            <label>Phone Number: <input type="text" name="phone" required placeholder="123-456-7890"></label>
            <label>Password: <input type="password" name="password" id="password" required placeholder="*********"></label>
			<label>Re-enter Password: <input type="password" name="confirm_password" id="confirm_password" required placeholder="*********"></label>
			<p id="passwordError" class="error" style="display:none;">❌ Passwords do not match!</p>


            <h3>Select User Type</h3>
            <div class="radio-group">
                <label><input type="radio" name="user_type" value="Alumni" onclick="updateForm()" required> Alumni</label>
                <label><input type="radio" name="user_type" value="Professor" onclick="updateForm()"> Professor</label>
                <label><input type="radio" name="user_type" value="Student" onclick="updateForm()"> Student</label>
                <label><input type="radio" name="user_type" value="Employer" onclick="updateForm()"> Employer</label>
                <label><input type="radio" name="user_type" value="Admin" onclick="updateForm()"> Admin</label>
            </div>

            <div id="alumniFields" class="hidden">
                <label>Major: <input type="text" name="major2"></label>
                <label>Graduation Year: <input type="number" name="graduation_year2"></label>
            </div>

            <div id="studentFields" class="hidden">
                <label>Major: <input type="text" name="major"></label>
                <label>Minor: <input type="text" name="minor"></label>
                <label>Graduation Year: <input type="number" name="graduation_year"></label>
            </div>

            <div id="employerFields" class="hidden">
                <label>Company Name: <input type="text" name="company"></label>
            </div>

            <input type="submit" value="Register">
			<p style="margin-top: 15px; font-size: 14px; color: #041E42;">
				Already have an account? 
				<a href="login.php" style="color: #0077C8; text-decoration: none; font-weight: bold;">
				Log in
				</a>
			</p>
        </form>
    </div>

</body>
</html>
