<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
</head>
<body>
    <form action="student_login.php" method="post">
        Username: <input type="text" name="uname" required>
        <br> Password: <input type="password" name="pword" required>
        <p><input type="submit" name="submit" value="Login"></p>
    </form>
</body>
</html>

<?php
session_start(); 

if (isset($_POST['submit'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "351delta";

    // Create a secure database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $uname = trim($_POST['uname']);
    $pword = trim($_POST['pword']);

    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT Student_Email, Password_Hash FROM student_account WHERE Student_Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_hash = $row['Password_Hash'];

        // Verify the password using password_verify()
        if (password_verify($pword, $stored_hash)) {
            $_SESSION['Student_Email'] = $row['Student_Email'];
            header("Location: main.php");
            exit();
        } else {
            echo "Incorrect username or password.";
        }
    } else {
        echo "Incorrect username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>


