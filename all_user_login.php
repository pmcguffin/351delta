<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
</head>
<body>
    <h2>Choose one</h2>
    <form action="" method="post">
        <input type="radio" id="Admin" name="classification" value="Admin" required> 
        <label for="Admin">Admin</label><br>
        <input type="radio" id="Alumni" name="classification" value="Alumni">
        <label for="Alumni">Alumni</label><br> 
        <input type="radio" id="Employer" name="classification" value="Employer">
        <label for="Employer">Employer</label><br>
        <input type="radio" id="Professor" name="classification" value="Professor"> 
        <label for="Professor">Professor</label><br>
        <input type="radio" id="Student" name="classification" value="Student"> 
        <label for="Student">Student</label><br> 
        
        <label for="username">Username:</label>
        <input name="uname" id="username" type="text" required><p>

        <label for="password">Password:</label>
        <input name="pword" id="password" type="password" required><p>

        <button type="submit" name="submit">Submit</button>
    </form>

    <?php
    session_start();

    include('Connection.php');

    if (isset($_POST['submit'])) {
        // $servername = "localhost";
        // $username = "root";
        // $password = "";
        // $dbname = "351delta";

        // $conn = new mysqli($servername, $username, $password, $dbname);

        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // }

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
                echo "Invalid classification.";
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
                // Clear all existing session variables to avoid conflicts
                session_unset();

                // Set the correct session variable for the user type
                $_SESSION[$email_column] = $row[$email_column];

                // Redirect to main.php
                header("Location: main.php");
                exit();
            } else {
                echo "<p>❌ Login failed. Incorrect password.</p>";
            }
        } else {
            echo "<p>❌ Login failed. User not found.</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
