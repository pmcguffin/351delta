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
	$dbname = "351test";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error) . "</br>";
	}
	
	$sql = "SELECT * FROM alumni_account WHERE Alumni_Email = " . $_POST['uname'] . "AND " . "";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		echo "Login Successful!";
	} else {
		echo "Login failed. Please try again.";
	}


	$conn->close();
}
?>