<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "351delta";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $user_type = $conn->real_escape_string($_POST['user_type']);
    $name = $conn->real_escape_string($_POST['name']);
    $major = $conn->real_escape_string($_POST['major']);
    $grad_year = $conn->real_escape_string($_POST['grad_year']);
    $company = $conn->real_escape_string($_POST['company']);

    // Insert into the "saved_contacts" table
    $sql = "INSERT INTO saved_contacts (user_type, name, major, graduation_year, company_name) 
            VALUES ('$user_type', '$name', '$major', '$grad_year', '$company')";

    if ($conn->query($sql) === TRUE) {
        echo "Contact saved successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
