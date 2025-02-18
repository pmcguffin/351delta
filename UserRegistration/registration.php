
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["user_type"];
    $email = trim($_POST["email"]);
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
			echo "Error: Email already exists in $table.";
			exit();
		}
	}


    switch ($userType) {
        case "Alumni":
            $major = trim($_POST["major"]);
            $grad_year = intval($_POST["graduation_year"]);
            $sql = "INSERT INTO Alumni_Account (Alumni_Email, Name, Phone_Number, Major, Graduation_Year, Password_Hash) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $email, $name, $phone, $major, $grad_year, $hashed_password);
            break;
        case "Professor":
            $sql = "INSERT INTO Professors_Account (Professor_Email, Name, Phone_Number, Password_Hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $name, $phone, $hashed_password);
            break;
        case "Student":
            $major = trim($_POST["major"]);
            $minor = trim($_POST["minor"]);
            $grad_year = intval($_POST["graduation_year"]);
            $sql = "INSERT INTO Student_Account (Student_Email, Name, Phone_Number, Minor, Major, Graduation_Year, Password_Hash) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $email, $name, $phone, $minor, $major, $grad_year, $hashed_password);
            break;
        case "Employer":
            $company = trim($_POST["company"]);
            $sql = "INSERT INTO Employers_Account (Employer_Email, Name, Phone_Number, Company_Name, Password_Hash) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $email, $name, $phone, $company, $hashed_password);
            break;
        case "Admin":
            $sql = "INSERT INTO Admin_Account (Admin_Email, Name, Phone_Number, Password_Hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $name, $phone, $hashed_password);
            break;
        default:
            echo "Error: Invalid user type.";
            $conn->close();
            exit();
    }

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

 
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script>
        function updateForm() {
            let userType = document.querySelector('input[name="user_type"]:checked').value;
            document.getElementById("studentFields").style.display = userType === "Student" ? "block" : "none";
            document.getElementById("alumniFields").style.display = userType === "Alumni" ? "block" : "none";
            document.getElementById("employerFields").style.display = userType === "Employer" ? "block" : "none";
        }
    </script>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="registration.php">
        <label>Email: <input type="email" name="email" required placeholder="example@cnu.edu"></label><br>
        <label>Name: <input type="text" name="name" required placeholder="John Doe"></label><br>
        <label>Phone Number: <input type="text" name="phone" required placeholder="123-456-7890"></label><br>
        <label>Password: <input type="password" name="password" required placeholder="*********"></label><br>

        <h3>Select User Type</h3>
        <label><input type="radio" name="user_type" value="Alumni" onclick="updateForm()" required> Alumni</label>
        <label><input type="radio" name="user_type" value="Professor" onclick="updateForm()"> Professor</label>
        <label><input type="radio" name="user_type" value="Student" onclick="updateForm()"> Student</label>
        <label><input type="radio" name="user_type" value="Employer" onclick="updateForm()"> Employer</label>
        <label><input type="radio" name="user_type" value="Admin" onclick="updateForm()"> Admin</label><br><br>

        <div id="alumniFields" style="display:none;">
            <label>Major: <input type="text" name="major"></label><br>
            <label>Graduation Year: <input type="number" name="graduation_year"></label><br>
        </div>

        <div id="studentFields" style="display:none;">
            <label>Major: <input type="text" name="major"></label><br>
            <label>Minor: <input type="text" name="minor"></label><br>
            <label>Graduation Year: <input type="number" name="graduation_year"></label><br>
        </div>

        <div id="employerFields" style="display:none;">
            <label>Company Name: <input type="text" name="company"></label><br>
        </div>

        <input type="submit" value="Register">
    </form>
</body>
</html>
