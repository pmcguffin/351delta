<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>   
  <title>Find Others</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    /* 001f3d */
    h1 {
      color: #003366; 
    }

    form.example {
      display: flex;
      justify-content: center;
      max-width: 600px;
      width: 100%;
      margin-bottom: 30px;
    }

    form.example input[type="text"] {
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      flex: 1;
      border-radius: 6px 0 0 6px;
      outline: none;
    }

    form.example button {
      padding: 12px 20px;
      background-color: #003366;
      color: white;
      border: none;
      border-radius: 0 6px 6px 0;
      cursor: pointer;
    }

    .results {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      width: 100%;
      max-width: 1100px;
    }

    .user-card {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      padding: 20px;
      width: 280px;
      transition: transform 0.2s;
    }

    .user-card:hover {
      transform: translateY(-5px);
    }

    .user-card h3 {
      margin: 0 0 10px;
      color: #003366;
    }

    .user-card p {
      margin: 6px 0;
      color: #444;
      font-size: 14px;
    }

    .add-btn {
      margin-top: 10px;
      background-color: #003366;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .add-btn.added {
      background-color: #28a745;
      cursor: default;
    }
  </style>
  <script>
    function changeButton(btn) {
      btn.textContent = "✔️ Added";
      btn.classList.add("added");
      btn.disabled = true;
    }
  </script>
</head>

<body>

  <h1>Find Others!</h1>
  <p>Search based on Major, Graduation Year, Company name, or Name:</p>
  <form class="example" action="findothers.php" method="post">
    <input type="text" placeholder="Major, Graduation Year, Name" name="search">
    <button type="submit"><i class="fa fa-search"></i></button>
  </form>

<?php
include('home_button.php'); 
if (isset($_POST['search'])) {
    $conn = new mysqli("localhost", "root", "", "351delta");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $search = $conn->real_escape_string($_POST['search']); 

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

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='results'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='user-card'>
                    <h3>" . htmlspecialchars($row["Name"]) . "</h3>
                    <p><strong>Type:</strong> " . $row["user_type"] . "</p>
                    <p><strong>Major:</strong> " . ($row["Major"] ?? 'N/A') . "</p>
                    <p><strong>Graduation Year:</strong> " . ($row["Graduation_Year"] ?? 'N/A') . "</p>
                    <p><strong>Company:</strong> " . ($row["Company_Name"] ?? 'N/A') . "</p>
                    <button type='button' class='add-btn' onclick='changeButton(this)'>Add</button>
                </div>";
        }
        echo "</div>";
    } else {
        echo "<p>No results found.</p>";
    }

    $conn->close();
}
?>

</body>
</html>

        




     
