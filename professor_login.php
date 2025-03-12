<html><body>
<form action="351deltatest.php" method="post">
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
	
	$sql = "SELECT * FROM Professor_email WHERE Professor_Email = " . $_POST['uname'] . " AND " . "Password_Hash = " . $_POST['pword'];
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		session_start();
		$row = $result->fetch_assoc();
		$_SESSION['Professor_Email'] = $row['Professor_Email'];
	} else {
		echo "Login failed. Please try again.";
	}


	$conn->close();
}
?>
