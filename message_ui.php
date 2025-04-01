<?php
// Include your existing message handling logic
include('message.php');

session_start();
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$user_type = $_SESSION['user_type'];

// Determine dashboard path dynamically
$dashboard_path = "/351/dashboards/" . strtolower($user_type) . "_dashboard.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($user_type); ?> Dashboard - Messages</title>
    <link rel="stylesheet" href="/351delta/css_style/css_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        #message-container {
            display: flex;
            height: 90vh;
        }

        #conversation-list {
            width: 30%;
            border-right: 1px solid #ccc;
            overflow-y: auto;
            padding: 10px;
            background: #fff;
        }

        #chat-window {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 10px;
            background: #fafafa;
        }

        .chat-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .message-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .message {
            margin-bottom: 10px;
        }

        .sent {
            text-align: right;
            color: blue;
        }

        .received {
            text-align: left;
            color: green;
        }

        #new-message-form {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        input, textarea, button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <h1><?php echo ucfirst($user_type); ?> Dashboard</h1>
        <nav>
            <a href="<?php echo $dashboard_path; ?>">Home</a>
            <a href="<?php echo $dashboard_path; ?>#messages">Messages</a>
        </nav>
    </header>

    <div id="message-container">

        <!-- Left: Conversation List -->
        <div id="conversation-list">
            <h2>Conversations</h2>
            <?php
            $sql = "SELECT DISTINCT receiver_email, sender_email 
                    FROM chat_information 
                    WHERE receiver_email = ? OR sender_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $user_email, $user_email);
            $stmt->execute();
            $conversations = $stmt->get_result();

            while ($row = $conversations->fetch_assoc()):
                $contact = ($row['receiver_email'] === $user_email) ? $row['sender_email'] : $row['receiver_email'];
            ?>
                <div>
                    <a href="?chat_with=<?php echo urlencode($contact); ?>">
                        <?php echo htmlspecialchars($contact); ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Right: Chat Window -->
        <div id="chat-window">
            <?php if (isset($_GET['chat_with'])):
                $chat_with = $_GET['chat_with'];
            ?>
                <div class="chat-header">Chat with: <?php echo htmlspecialchars($chat_with); ?></div>
                <div class="message-list">
                    <?php
                    $chat_query = "SELECT * FROM chat_information 
                                   WHERE (sender_email = ? AND receiver_email = ?) 
                                      OR (sender_email = ? AND receiver_email = ?) 
                                   ORDER BY chat_time ASC";
                    $stmt = $conn->prepare($chat_query);
                    $stmt->bind_param("ssss", $user_email, $chat_with, $chat_with, $user_email);
                    $stmt->execute();
                    $messages = $stmt->get_result();

                    while ($message = $messages->fetch_assoc()):
                        $class = ($message['sender_email'] == $user_email) ? 'sent' : 'received';
                    ?>
                        <div class="message <?php echo $class; ?>">
                            <?php echo htmlspecialchars($message['message_contents']); ?>
                            <br><small><?php echo $message['chat_time']; ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Message Form -->
                <form id="new-message-form" method="POST" action="message.php">
                    <input type="hidden" name="receiver_email" value="<?php echo htmlspecialchars($chat_with); ?>">
                    <textarea name="message" placeholder="Type a message..." required></textarea>
                    <button type="submit">Send</button>
                </form>
            <?php else: ?>
                <p>Select a conversation to start chatting.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>
