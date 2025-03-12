<?php
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

// Test user credentials
$email = "teststudent@email.com";
$plain_password = "password123"; // Plain text password

// Hash the password before storing it
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Insert test user
$sql = "INSERT INTO Student_Account (Student_Email, Name, Phone_Number, Minor, Major, Graduation_Year, Password_Hash)
        VALUES (?, 'Test Student', '123-456-7890', 'Math', 'Computer Science', 2025, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo "Test user created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
