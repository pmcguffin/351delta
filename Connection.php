<?php
// Josh's code
$servername = "localhost"; 
$username = "root";
$password = ""; 
$dbname = "351delta"; 

// Create Connection 
$conn = new mysqli($servername, $username, $password, $dbname); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); 
}
?> 
