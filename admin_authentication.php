<html><body>
<form action="admin_authentication.php" method="post">
    <label for="username">Username:</label>
    <input name="username" id="username" type="text"><p>

    <label for="password">Password:</label>
    <input name="password" id="password" type="text"><p>

    <button type="submit">Submit</button>
</form>

<?php

if(isset($_POST['submit'])){
	$servername = "localhost"; 
	$username = "root";
	$password = ""; 
	$dbname = "351delta"; 

// Create Connection 
	$conn = new mysqli($servername, $username, $password, $dbname); 
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error); 
	}
	
	$sql = "SELECT * FROM admin_account WHERE Admin_Email = " . $_POST['uname'] . " AND " . "Password_Hash = " . $_POST['pword'];
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		session_start();
		$row = $result->fetch_assoc();
		$_SESSION['Admin_Email'] = $row['Admin_Email'];
	} else {
		echo "Login failed. Please try again.";
	}


	$conn->close();
}
		
	
?> 