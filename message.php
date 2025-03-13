<?php
session_start();
include('Connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['Alumni_Email'])) {
    header("Location: login.php");
    exit();
}

// Define the current user's email and role
$user_email = $_SESSION['Alumni_Email'];
$chatter = "Alumni";

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_email'], $_POST['receiver_type'], $_POST['message'])) {
    $receiver_email = trim($_POST['receiver_email']);
    $receiver_type = trim($_POST['receiver_type']); // Determines if the receiver is an admin, student, professor, etc.
    $message = trim($_POST['message']);

    // Build dynamic SQL query to check if a chat session already exists
    $sql = "SELECT chat_id FROM chat WHERE alumni_email = ? AND $receiver_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_email, $receiver_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If chat session exists, fetch chat_id
        $row = $result->fetch_assoc();
        $chat_id = $row['chat_id'];
    } else {
        // If no chat session exists, create a new one
        $insert_chat = "INSERT INTO chat (alumni_email, $receiver_type) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_chat);
        $stmt->bind_param("ss", $user_email, $receiver_email);
        $stmt->execute();
        $chat_id = $stmt->insert_id;
    }

    // Insert the message into the chat_information table
    $insert_message = "INSERT INTO chat_information (chat_id, chat_time, chat_data, message_contents) VALUES (?, NOW(), '', ?)";
    $stmt = $conn->prepare($insert_message);
    $stmt->bind_param("is", $chat_id, $message);
    $stmt->execute();
}

// Retrieve messages for the logged-in alumni
$sql = "SELECT ci.chat_time, ci.message_contents, c.chatter FROM chat_information ci
        JOIN chat c ON ci.chat_id = c.chat_id
        WHERE c.alumni_email = ?
        ORDER BY ci.chat_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard - Messages</title>
    <link rel="stylesheet" href="css_style.css">
</head>
<body>
    <header>
        <h1>Alumni Dashboard</h1>
        <nav>
            <a href="alumni_dashboard.php">Home</a>
            <a href="alumni_dashboard.php#messages">Messages</a>
        </nav>
    </header>

    <div id="messages">
        <h2>Messages</h2>
        <ul>
            <?php while ($row = $messages->fetch_assoc()): ?>
                <li><strong><?php echo $row['chatter']; ?>:</strong> <?php echo $row['message_contents']; ?> <em>(<?php echo $row['chat_time']; ?>)</em></li>
            <?php endwhile; ?>
        </ul>

        <h2>Send a Message</h2>
        <form method="POST">
            <label for="receiver_email">Receiver Email:</label>
            <input type="email" name="receiver_email" required>
            
            <label for="receiver_type">Receiver Type:</label>
            <select name="receiver_type" required>
                <option value="admin_email">Admin</option>
                <option value="employer_email">Employer</option>
                <option value="professor_email">Professor</option>
                <option value="student_email">Student</option>
                <option value="alumni_email">Alumni</option>
            </select>
            
            <label for="message">Message:</label>
            <textarea name="message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
