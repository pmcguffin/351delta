<?php
// Josh's code
session_start();
include('Connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Get current user information
$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_type'];

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_email'], $_POST['message'])) {
    $receiver_email = trim($_POST['receiver_email']);
    $message = trim($_POST['message']);

    date_default_timezone_set('America/New_York');
    $current_time = date('Y-m-d H:i:s');
    echo $current_time;

    
    $check_chat = "SELECT * FROM chat WHERE Sender_Email = ? AND Receiver_Email = ? AND Chat_Time = ?";
    $stmt_check = $conn->prepare($check_chat);
    $stmt_check->bind_param("sss", $user_email, $receiver_email, $current_time);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($stmt_check === false) {
        echo "❌ Error preparing query: " . $conn->error;
        exit();
    }

    

$stmt_check->execute();

// Check if query executed successfully
if ($stmt_check->error) {
    echo "❌ Query Execution Error: " . $stmt_check->error;
    exit();
}

$result_check = $stmt_check->get_result();

// Debug output to check what's happening
// echo "Executed query: SELECT * FROM chat WHERE Sender_Email = '$user_email' AND Receiver_Email = '$receiver_email'<br>";
if ($result_check->num_rows === 0) {
    $insert_chat = "INSERT INTO chat (Sender_Email, Receiver_Email, Chat_Time) VALUES (?, ?, ?)";
    $stmt_insert_chat = $conn->prepare($insert_chat);
    $stmt_insert_chat->bind_param("sss", $user_email, $receiver_email, $current_time);
    $stmt_insert_chat->execute();
    $stmt_insert_chat->close();
}

$stmt_check->close();

// Insert the message into the chat_information table
$insert_message = "INSERT INTO chat_information (chat_time, receiver_email, sender_email, chat_data, message_contents) 
                   VALUES (?, ?, ?, '', ?)";
$stmt_message = $conn->prepare($insert_message);
$stmt_message->bind_param("ssss", $current_time, $receiver_email, $user_email, $message);
$stmt_message->execute();
$stmt_message->close();
}


// Retrieve messages for the logged-in user
$sql = "SELECT ci.chat_time, ci.message_contents, c.chatter 
        FROM chat_information ci 
        JOIN chat c ON ci.receiver_email = c.receiver_email AND ci.sender_email = c.sender_email AND ci.chat_time = c.Chat_Time
        WHERE ci.receiver_email = ? OR ci.sender_email = ? 
        ORDER BY ci.chat_time DESC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_email, $user_email);
$stmt->execute();
$messages = $stmt->get_result();

// Determine dashboard path dynamically
$dashboard_path = "/351/dashboards/" . strtolower($user_type) . "_dashboard.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($user_type); ?> Dashboard - Messages</title>
    <link rel="stylesheet" href="/351delta/css_style/css_style.css">
</head>
<body>
    <header>
        <h1><?php echo ucfirst($user_type); ?> Dashboard</h1>
        <nav>
            <a href="<?php echo $dashboard_path; ?>">Home</a>
            <a href="<?php echo $dashboard_path; ?>#messages">Messages</a>
        </nav>
    </header>

    <div id="messages">
        <h2>Messages</h2>
        <ul>
            <?php while ($row = $messages->fetch_assoc()): ?>
                <li><strong><?php echo htmlspecialchars($row['chatter']); ?>:</strong> <?php echo htmlspecialchars($row['message_contents']); ?> <em>(<?php echo $row['chat_time']; ?>)</em></li>
            <?php endwhile; ?>
        </ul>

        <h2>Send a Message</h2>
        <form method="POST">
            <label for="receiver_email">Receiver Email:</label>
            <input type="email" name="receiver_email" required>

            <label for="message">Message:</label>
            <textarea name="message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
