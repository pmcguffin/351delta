<?php
session_start();
include('Connection.php');

if (!isset($_SESSION['user_email']) || !isset($_POST['contact_email'])) {
    exit();
}

$user_email = $_SESSION['user_email'];
$contact_email = $_POST['contact_email'];

// Fetch messages between the logged-in user and the selected contact
$query = "SELECT sender_email, message_contents, chat_time FROM chat_information 
          WHERE (sender_email = ? AND receiver_email = ?) 
             OR (sender_email = ? AND receiver_email = ?) 
          ORDER BY chat_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $user_email, $contact_email, $contact_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = ($row['sender_email'] == $user_email) ? 'sent' : 'received';
    $timestamp = date('g:i A', strtotime($row['chat_time'])); // Format the timestamp
    echo "<div class='message $class'>";
    echo "<div class='message-content'>";
    echo "<strong>" . htmlspecialchars($row['sender_email']) . ":</strong> ";
    echo htmlspecialchars($row['message_contents']);
    echo "</div>";
    echo "<div class='timestamp'>$timestamp</div>";
    echo "</div>";
}

$stmt->close();
?>