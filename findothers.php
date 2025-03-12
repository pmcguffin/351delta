<html>
<body>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Others</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #001f3d; /* Navy Blue */
            color: white;
            padding: 15px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }
        /* Add this to your CSS */
        
        input[type="text"], input[type="password"], input[type="radio"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #001f3d; /* Navy Blue */
            color: white;
            border: none;
            padding: 12px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005bb5; /* Lighter blue on hover */
        }

        .radio-label {
            margin: 10px 0;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #001f3d;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

		.error-container {
			display: flex;
			justify-content: center;  /* Centers horizontally */
			align-items: center;  /* Aligns towards the top */
			height: 20vh;  /* Reduces whitespace */
			flex-direction: column;
			margin-top: 20px; /* Adds a small gap from the top */
		}

		.error-message {
			background-color: #ffcccc;
			color: #d8000c;
			text-align: center;
			padding: 12px;
			font-size: 16px;
			font-weight: bold;
			border: 1px solid #d8000c;
			border-radius: 5px;
			width: 50%;
			max-width: 500px;
		}


    </style>



</head>


<h1>Find Others!</h1>
<p>Search based on Major, Graduation Year, Company name, or Name:</p>  

<form class="example" action="findothers.php" method="post">
  <input type="text" placeholder="Major, Graduation Year, Name" name="search">
  <button type="submit"><i class="fa fa-search"></i></button>
</form>

<br>

<?php
if (isset($_POST['search'])) {
    // Database connection details
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

    // Sanitize user input
    $search = $conn->real_escape_string($_POST['search']); 

    // SQL query with UNION
    $sql = "
        SELECT 'Admin' AS user_type, Name, NULL AS Major, NULL AS Graduation_Year, NULL AS Company_Name 
        FROM Admin_Account 
        WHERE Name LIKE '%$search%'
        
        UNION 
        
        SELECT 'Alumni' AS user_type, Name, Major, Graduation_Year, NULL AS Company_Name
        FROM Alumni_Account 
        WHERE Name LIKE '%$search%' 
        OR Major LIKE '%$search%' 
        OR Graduation_Year LIKE '%$search%'
        
        UNION 
        
        SELECT 'Employer' AS user_type, Name, NULL AS Major, NULL AS Graduation_Year, Company_Name
        FROM Employers_Account 
        WHERE Name LIKE '%$search%' 
        OR Company_Name LIKE '%$search%'
        
        UNION 
        
        SELECT 'Professor' AS user_type, Name, NULL AS Major, NULL AS Graduation_Year, NULL AS Company_Name
        FROM Professors_Account 
        WHERE Name LIKE '%$search%'
        
        UNION 
        
        SELECT 'Student' AS user_type, Name, Major, Graduation_Year, NULL AS Company_Name
        FROM Student_Account 
        WHERE Name LIKE '%$search%' 
        OR Major LIKE '%$search%' 
        OR Graduation_Year LIKE '%$search%'
    ";

    // Execute query
    $result = $conn->query($sql);

    // Check if results are found
    if ($result->num_rows > 0) {
        echo "<h2>Search Results:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Major</th>
                    <th>Graduation Year</th>
                    <th>Company Name</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["user_type"] . "</td>
                    <td>" . $row["Name"] . "</td>
                    <td>" . ($row["Major"] ? $row["Major"] : 'N/A') . "</td>
                    <td>" . ($row["Graduation_Year"] ? $row["Graduation_Year"] : 'N/A') . "</td>
                    <td>" . ($row["Company_Name"] ? $row["Company_Name"] : 'N/A') . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }

    // Close the connection
    $conn->close();
}
?>

</body>
</html>
