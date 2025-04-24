<?php
// Josh's code
session_start();
include('Connection.php');
include('home_button.php'); 

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Fetch recent contacts
$query = "SELECT DISTINCT 
            CASE 
                WHEN sender_email = ? THEN receiver_email 
                ELSE sender_email 
            END AS contact_email 
          FROM chat_information 
          WHERE sender_email = ? OR receiver_email = ?
          ORDER BY chat_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $user_email, $user_email, $user_email);
$stmt->execute();
$contacts = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <link rel="stylesheet" href="css_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }
        .chat-container {
            display: flex;
            width: 100%;
            height: 100%;
        }
        .sidebar {
            width: 30%;
            background: #f1f1f1;
            padding: 10px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
        }
        .contact {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .contact:hover {
            background: #ddd;
        }
        .chat-window {
            width: 70%;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .chat-header {
            padding: 10px;
            background: #003366;
            color: white;
            text-align: center;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .message {
            padding: 10px;
            margin: 5px;
            border-radius: 10px; 
            max-width: 50%;
            position: relative;
            display: inline-block;
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: break-word;
        }
        .sent {
            align-self: flex-end;
            background: #003366;
            color: white;
        }
        .received {
            align-self: flex-start;
            background: #e5e5e5;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .chat-input textarea {
            flex-grow: 1;
            padding: 5px;
        }
        .chat-input button {
            padding: 5px 10px;
            background: #003366;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="sidebar">
            <h2>Chats</h2>
            <button id="new-chat">New Chat</button>
            <div id="contact-list">
                <?php while ($row = $contacts->fetch_assoc()): ?>
                    <div class="contact" data-email="<?php echo htmlspecialchars($row['contact_email']); ?>">
                        <?php echo htmlspecialchars($row['contact_email']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="chat-window">
            <div class="chat-header">Select a chat</div>
            <div id="chat-messages" class="chat-messages"></div>
            <div class="chat-input">
                <input type="hidden" id="receiver_email">
                <textarea id="message" placeholder="Type a message..."></textarea>
                <button id="send-message">Send</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let selectedContact = '';

            $('.contact').click(function () {
                selectedContact = $(this).data('email');
                $('#receiver_email').val(selectedContact);
                $('.chat-header').text(selectedContact);
                loadMessages(selectedContact);
            });

            $('#new-chat').click(function () {
                let newEmail = prompt("Enter the email of the person you want to chat with:");
                if (newEmail) {
                    selectedContact = newEmail;
                    $('#receiver_email').val(selectedContact);
                    $('.chat-header').text(selectedContact);
                    loadMessages(selectedContact);
                }
            });

            function loadMessages(contactEmail) {
                $.post('load_messages.php', { contact_email: contactEmail }, function (data) {
                    $('#chat-messages').html(data);
                });
            }

            $('#send-message').click(function () {
                let message = $('#message').val();
                if (message.trim() === '' || selectedContact === '') return;
                $.post('message.php', { receiver_email: selectedContact, message: message }, function () {
                    $('#message').val('');
                    loadMessages(selectedContact);
                });
            });

            setInterval(function () {
                if (selectedContact !== '') {
                    loadMessages(selectedContact);
                }
            }, 3000);
        });
    </script>
</body>
</html>
