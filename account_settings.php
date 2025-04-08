<?php
session_start();
include('Connection.php');

if (empty($_SESSION)) {
    header("Location: login.php");
    exit();
}

$email = "";
$userType = "";

$tables = [
    "Admin" => ["table" => "admin_account", "email_column" => "Admin_Email"],
    "Alumni" => ["table" => "alumni_account", "email_column" => "Alumni_Email"],
    "Employer" => ["table" => "employers_account", "email_column" => "Employer_Email"],
    "Professor" => ["table" => "professors_account", "email_column" => "Professor_Email"],
    "Student" => ["table" => "student_account", "email_column" => "Student_Email"],
];

foreach ($tables as $type => $info) {
    if (isset($_SESSION[$info["email_column"]])) {
        $email = $_SESSION[$info["email_column"]];
        $userType = $type;
        break;
    }
}

if (empty($email) || empty($userType)) {
    header("Location: login.php");
    exit();
}

$table = $tables[$userType]["table"];
$email_column = $tables[$userType]["email_column"];

$error = "";
$success = "";

// Fetch user data
$query = "SELECT Name, Phone_Number";
if ($userType == "Student" || $userType == "Alumni") {
    $query .= ", Major, Graduation_Year";
} elseif ($userType == "Employer") {
    $query .= ", Company_Name";
}
$query .= " FROM $table WHERE $email_column = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updated_sections = [];

    $new_email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($userType == "Student" || $userType == "Alumni") {
        $major = trim($_POST["major"]);
        $grad_year = trim($_POST["graduation_year"]);
    } elseif ($userType == "Employer") {
        $company_name = trim($_POST["company_name"]);
    }

    if (!empty($password) && $password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    }

    // Check if email changed
    if ($new_email !== $email) {
        $updated_sections[] = "Email updated successfully!";
    }

    // Check if phone changed
    if ($phone !== $user['Phone_Number']) {
        $updated_sections[] = "Phone number updated successfully!";
    }

    if ($userType == "Student" || $userType == "Alumni") {
        if ($major !== $user['Major']) {
            $updated_sections[] = "Major updated successfully!";
        }
        if ($grad_year !== $user['Graduation_Year']) {
            $updated_sections[] = "Graduation year updated successfully!";
        }
    } elseif ($userType == "Employer") {
        if ($company_name !== $user['Company_Name']) {
            $updated_sections[] = "Company name updated successfully!";
        }
    }

    if (!empty($password)) {
        $updated_sections[] = "Password updated successfully!";
    }

    if (empty($error)) {
        $update_query = "UPDATE $table SET $email_column = ?, Phone_Number = ?";
        $params = [$new_email, $phone];
        $types = "ss";

        if ($userType == "Student" || $userType == "Alumni") {
            $update_query .= ", Major = ?, Graduation_Year = ?";
            $params[] = $major;
            $params[] = $grad_year;
            $types .= "ss";
        } elseif ($userType == "Employer") {
            $update_query .= ", Company_Name = ?";
            $params[] = $company_name;
            $types .= "s";
        }

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query .= ", Password_Hash = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $update_query .= " WHERE $email_column = ?";
        $params[] = $email;
        $types .= "s";

        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param($types, ...$params);

        if ($update_stmt->execute()) {
    $_SESSION[$email_column] = $new_email;
    
    if (!empty($updated_sections)) {
        $success = "✅ " . implode("<br>✅ ", $updated_sections);
    } else {
        $success = "✅ No changes were made.";
    }

    // ✅ Refetch the updated user data
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $new_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $error = "❌ Error updating profile: " . $update_stmt->error;
}

        $update_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #041E42;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 60px auto;
            padding: 25px;
            background-color: white;
            color: #041E42;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        h2 {
            text-align: center;
            color: #041E42;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #041E42;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #0077C8;
            color: white;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }

        button:hover {
            background-color: #005A9C;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #041E42;
        }

        .back-link a {
            color: #0077C8;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (!empty($error)): ?>
            <div class="error-container">
                <div class="error-message"><?php echo $error; ?></div>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="success-container">
                <div class="success-message"><?php echo $success; ?></div>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['Phone_Number']); ?>" required>

            <?php if ($userType == "Student" || $userType == "Alumni"): ?>
                <label>Major:</label>
                <input type="text" name="major" value="<?php echo htmlspecialchars($user['Major']); ?>" required>

                <label>Graduation Year:</label>
                <input type="text" name="graduation_year" value="<?php echo htmlspecialchars($user['Graduation_Year']); ?>" required>
            <?php endif; ?>

            <label>New Password (leave blank to keep current password):</label>
            <input type="password" name="password">

            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password">

            <button type="submit">Update Profile</button>
        </form>

        <p class="back-link">
            <a href="main.php">⬅ Back to Main Page</a>
        </p>
    </div>

</body>
</html>
