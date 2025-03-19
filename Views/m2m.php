<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../Entity/userEntity.php";
require_once('../övrigt/PDO.php');
require_once "../Dao/DmDAO.php";
session_start();


if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $user_id = $user->getId();
} else {
    header('Location: index.php');
}
$other_user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : null;

if (!$other_user_name) {
    die(" ERROR: Missing username.");
}

// get info on sender
$dmDao = new DmDAO($pdo);
$other_user = $dmDao->idOtherUser($other_user_name);

if (!$other_user) {
    die(" ERROR: User not found.");
}
// update messages
$other_user_id = (int) $other_user['id'];
$dmDao = new DmDAO($pdo);
$dmDao->updateStmt($user_id, $other_user_id);

error_log("User ID: $user_id, Other User ID: $other_user_id");



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save'])) {
    $message_content = !empty($_POST['message']) ? trim($_POST['message']) : NULL;
    $message_image = NULL;


    if (!empty($_FILES['message_image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['message_image']['tmp_name']);
        $message_image = base64_encode($imageData);
    }

    if (!$message_content && !$message_image) {
        die(" ERROR: Cannot send an empty message.");
    }
    // insert new message
    $true = 1;
    $dmDao = new DmDAO($pdo);
    $dmDao->insertMessages($message_content, $message_image, $user_id, $other_user_id, $true);
}

// load full conversation
$dmDao = new DmDAO($pdo);
$messages = $dmDao->getConversation($user_id, $other_user_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Chat</title>
</head>

<body>
    <?php require "navbar.php" ?>
    <div class="chat-room">
        <div class="card-header">
            <p>Chat with <?php echo htmlspecialchars($other_user_name); ?></p>
        </div>
        <!-- loop out chat messages -->
        <div class="chat-messages">
            <?php foreach ($messages as $msg): ?>
                <?php if ($msg['user1_id'] == $user_id): ?>
                    <div class="sent-chat">
                        <p id="sender-name">
                            <strong>You:</strong>
                        </p>

                        <?php if (!empty($msg['message_content'])): ?>
                            <p id="your-message" onclick="toggleDate(this)">
                                <?php echo htmlspecialchars($msg['message_content']) ?>
                            </p>
                            <p id="your-message-date" class="message-date">
                                <?php echo "&nbsp" . htmlspecialchars($msg['CreatedDate']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($msg['message_image'])): ?>
                            <img class="message-img"
                                src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="400">
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="recieved-chat">
                        <p id="reciever-name">
                            <strong><?php echo htmlspecialchars($msg['user1_name']); ?>:</strong>
                        </p>
                        <?php if (!empty($msg['message_content'])): ?>
                            <p id="his-message" onclick="toggleDate(this)">
                                <?php echo htmlspecialchars($msg['message_content']) ?>
                            </p>
                            <p id="his-message-date" class="message-date">
                                <?php echo "&nbsp" . htmlspecialchars($msg['CreatedDate']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($msg['message_image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="150">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

                            <!-- send messages -->
        <form class="m2m" onsubmit="sendMessage(); return false;" method="POST" enctype="multipart/form-data">
            <div class="file-input-wrapper">
                <textarea id="messageContent" name="message" rows="4" placeholder="Skriv ditt inlägg här..."></textarea>
                <!-- Updated File Input in Form -->
                <label for="message_image" class="file-upload-label">Choose a file</label>
                <span id="file-name">No file chosen</span>
                <input type="file" id="message_image" name="message_image">
            </div>
            <button type="submit" name="save" class="btn btn-primary">Send</button>
            <a href="../Views/blogwall.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        function scrollToBottom() {
            var chatMessages = document.querySelector(".chat-messages");
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        function toggleDate(messageElement) {
            var dateElement = messageElement.nextElementSibling; // Get the date next to the message
            if (dateElement && dateElement.classList.contains("message-date")) {
                dateElement.style.display = dateElement.style.display === "none" ? "block" : "none";
            }
        }

        window.onload = function () {
            scrollToBottom();
        };

        document.querySelector("form").addEventListener("submit", function () {
            setTimeout(scrollToBottom, 100);
        });
        document.getElementById("message_image").addEventListener("change", function () {
            var fileName = this.files[0] ? this.files[0].name : "No file chosen";
            document.getElementById("file-name").textContent = fileName;
        });
        const socket = new WebSocket("ws://localhost:8080");

        socket.onopen = function () {
            console.log("WebSocket connection established.");
        };

        socket.onmessage = function (event) {
            const data = JSON.parse(event.data);
            console.log("Received message:", data);

            // Get the chat message container
            const chatMessages = document.querySelector(".chat-messages");

            // Create the message element
            let messageElement;

            // Check if the message was sent by the current user
            if (data.user_id == <?php echo json_encode($user_id); ?>) {
                // Sent message
                messageElement = document.createElement("div");
                messageElement.classList.add("sent-chat");

                // Add the sender name
                const senderName = document.createElement("p");
                senderName.id = "sender-name";
                senderName.innerHTML = "<strong>You:</strong>";
                messageElement.appendChild(senderName);

                // Add the message content only if it’s not empty
                if (data.message && data.message.trim() !== "") {
                    const messageContent = document.createElement("p");
                    messageContent.id = "your-message";
                    messageContent.innerHTML = data.message; // Display message content
                    messageElement.appendChild(messageContent);
                }

                // Add the message date (if any)
                const messageDate = document.createElement("p");
                messageDate.id = "your-message-date";
                messageDate.classList.add("message-date");
                messageDate.innerHTML = `&nbsp;${data.timestamp}`;
                messageElement.appendChild(messageDate);

                // If the message has an image
                if (data.message_image) {
                    const messageImage = document.createElement("img");
                    messageImage.classList.add("message-img");
                    messageImage.src = "data:image/jpeg;base64," + data.message_image;
                    messageImage.width = 400;
                    messageImage.classList.add("message-img");
                    messageElement.appendChild(messageImage);
                }
            } else {
                // Received message
                messageElement = document.createElement("div");
                messageElement.classList.add("recieved-chat");

                // Add the receiver name
                const receiverName = document.createElement("p");
                receiverName.id = "reciever-name";
                receiverName.innerHTML = `<strong><?php echo htmlspecialchars($other_user_name); ?>:</strong>`;
                messageElement.appendChild(receiverName);

                // Add the message content only if it’s not empty
                if (data.message && data.message.trim() !== "") {
                    const messageContent = document.createElement("p");
                    messageContent.id = "his-message";
                    messageContent.innerHTML = data.message; // Display message content
                    messageElement.appendChild(messageContent);
                }

                // Add the message date (if any)
                const messageDate = document.createElement("p");
                messageDate.id = "his-message-date";
                messageDate.classList.add("message-date");
                messageDate.innerHTML = `&nbsp;${data.timestamp}`;
                messageElement.appendChild(messageDate);


                // If the message has an image
                if (data.message_image) {
                    const messageImage = document.createElement("img");
                    messageImage.src = "data:image/jpeg;base64," + data.message_image;
                    messageImage.width = 150;
                    messageElement.appendChild(messageImage);
                }
            }

            // Append the constructed message element to the chat window
            chatMessages.appendChild(messageElement);

            // Scroll to the bottom to show the latest message
            scrollToBottom();
        };


        socket.onerror = function (error) {
            console.error("WebSocket error:", error);
        };

        socket.onclose = function () {
            console.log("WebSocket connection closed.");
        };

        function sendMessage() {
            const messageInput = document.getElementById("messageContent");
            const fileInput = document.getElementById("message_image");

            const message = messageInput.value.trim();
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const base64Image = e.target.result.replace(/^data:image\/[a-zA-Z]+;base64,/, '');

                    socket.send(JSON.stringify({
                        message: message, // could be empty if only file
                        message_image: base64Image,
                        filename: file.name,
                        user_id: <?php echo json_encode($user_id); ?>,
                        other_user_id: <?php echo json_encode($other_user_id); ?>
                    }));

                    // Clear inputs
                    messageInput.value = "";
                    fileInput.value = "";
                    document.getElementById("file-name").textContent = "No file chosen";
                };

                reader.readAsDataURL(file);
            } else if (message) {
                socket.send(JSON.stringify({
                    message: message,
                    message_image: null,
                    user_id: <?php echo json_encode($user_id); ?>,
                    other_user_id: <?php echo json_encode($other_user_id); ?>
                }));

                messageInput.value = "";
            }
        }
        // Add an event listener for the 'Enter' key to submit the form
        document.getElementById("messageContent").addEventListener("keydown", function (event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault(); // Prevent new line in textarea
                sendMessage(); // Call the sendMessage function
            } else if (event.key === "Enter" && event.shiftKey) {
                // Allow new line if Shift key is pressed
                return;
            }
        });
    </script>
</body>

</html>