<!DOCTYPE html>
<html>
<head>
    <title>Account Creation</title>
</head>
<body>

<form action="" method="post">

	<label for="Full Name">Name:</label>
    <input name="name" id="name" type="text" required><p>
	
	<label for="Email">Email:</label>
    <input name="email" id="email" type="text" required><p>
	
	<label for="Phone Number">Phone:</label>
    <input name="phone" id="phone" type="text" required><p>
	
	<label for="Major">Major:</label>
    <input name="major" id="major" type="text" required><p>
	
	<label for="Minor">Minor:</label>
    <input name="minor" id="minor" type="text" required><p>
	
	<label for="Graduation Year">Grad Year:</label>
    <input name="gyear" id="gyear" type="text" required><p>

    <label for="password">Password:</label>
    <input name="pword" id="password" type="password" required><p>
	
	<label for="confirm password">Confirm Password:</label>
    <input name="confpword" id="confpassword" type="password" required><p>

    <button type="submit">Submit</button>
</form></body></html>

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

    $name = $_['name'];
	$email = $_['email'];
	$phone = $_['phone'];
	$major = $_['major'];
	$minor = $_['minor'];
	$gyear = $_['gyear'];
	$plain_password = $_['pword']; // Plain text password

	// Hash the password before storing it
	$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

	// Insert test user
	$sql = "INSERT INTO Student_Account (Student_Email, Name, Phone_Number, Minor, Major, Graduation_Year, Password_Hash)
			VALUES (?, ?, ?, ?, ?, ?, ?)";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sssssss", $name, $email, $phone, $major, $minor, $gyear, $hashed_password);

	if ($stmt->execute()) {
		echo "Account created successfully!";
	} else {
		echo "Error: " . $stmt->error;
	}

	$stmt->close();
    $conn->close();
}
?>

