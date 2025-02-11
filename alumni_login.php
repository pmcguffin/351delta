<html><body>
<form action="alumni_login.php" method="post">
Username: <input type="text" name="uname">
<br> Password: <input type="text" name="pword"> <p>
<input type="submit" name="submit">
</form></body></html>
<?php
if(isset($_POST['submit'])) {

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "351delta";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error) . "</br>";
	}
	
	$sql = "SELECT * FROM alumni_account WHERE Alumni_Email = '" . $_POST['uname'] . "' AND  Password_Hash = '" . $_POST['pword']."'";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		session_start();
		$row = $result->fetch_assoc();
		$_SESSION['Alumni_Email'] = $row['Alumni_Email'];
		// main.php is a placeholder for the main paige and doesn't exist
		header("Location: main.php");
	} else {
		echo "Login failed. Please try again.";
	}


	$conn->close();
}
?>