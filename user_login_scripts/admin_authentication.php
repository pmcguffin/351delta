<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>

<form action="" method="post">
    <label for="username">Username:</label>
    <input name="uname" id="username" type="text" required><p>

    <label for="password">Password:</label>
    <input name="pword" id="password" type="password" required><p>

    <button type="submit">Submit</button>
</form>

<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $servername = "localhost"; 
    $username = "root";
    $password = ""; 
    $dbname = "351delta"; 

    // Create Connection 
    $conn = new mysqli($servername, $username, $password, $dbname); 

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); 
    }

    // Get form input
    $admin_email = trim($_POST['uname']);
    $entered_password = $_POST['pword'];

    // Prepared statement to fetch the stored password hash
    $sql = "SELECT Admin_Email, Password_Hash FROM admin_account WHERE Admin_Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if the admin exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $hashed_password);
        $stmt->fetch();

        // Verify password hash
        if ($entered_password === $hashed_password) {
            $_SESSION['Admin_Email'] = $email;  // Store session
            header("Location: main.php");  // Redirect to main page
            exit();
        } else {
            echo "<p>❌ Login failed. Incorrect password.</p>";
        }
    } else {
        echo "<p>❌ Login failed. Admin not found.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
