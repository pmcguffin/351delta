<html><body>
<form action="student_login.php" method="post">
Username: <input type="text" name="uname">
<br> Password: <input type="text" name="pword"> <p>
<input type="submit" name="submit">
</form></body></html>

<?php
session_start(); // Start session

if(isset($_POST['submit'])) {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "351delta";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $uname = trim($_POST['uname']);
    $pword = trim($_POST['pword']);


	
	$sql = "SELECT * FROM student_account WHERE Student_Email = ?";  
	$stmt = $conn->prepare($sql);  
	$stmt->bind_param("s", $_POST['uname']);  
	$stmt->execute();  
	$result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_hash = $row['Password_Hash'];


        if (password_verify($pword, $stored_hash)) {
            $_SESSION['username'] = $row['Student_Email']; 
            header("Location: main.php"); 
            exit();
        } else {
            echo "Incorrect username or password.";
        }
    } else {
        echo "Incorrect username or password.";
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
