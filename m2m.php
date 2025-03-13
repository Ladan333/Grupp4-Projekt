<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('PDO.php');
session_start();


if (!isset($_SESSION['id'])) {
    die(" ERROR: No user logged in.");
}

$user_id = (int) $_SESSION['id'];
$other_user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : null;

if (!$other_user_name) {
    die(" ERROR: Missing username.");
}


$stmt = $pdo->prepare("SELECT id FROM users WHERE user_name = :user_name");
$stmt->bindParam(':user_name', $other_user_name, PDO::PARAM_STR);
$stmt->execute();
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    die(" ERROR: User not found.");
}

$other_user_id = (int) $other_user['id'];


error_log("User ID: $user_id, Other User ID: $other_user_id");


$updateStmt = $pdo->prepare("
    UPDATE dms 
    SET unread_status = 0 
    WHERE unread_status = 1 
    AND (
        (user1_id = :user_id1 AND user2_id = :other_user_id1) 
        OR (user1_id = :other_user_id2 AND user2_id = :user_id2)
    )
");
$updateStmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
$updateStmt->bindParam(':other_user_id1', $other_user_id, PDO::PARAM_INT);
$updateStmt->bindParam(':other_user_id2', $other_user_id, PDO::PARAM_INT);
$updateStmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
$updateStmt->execute();

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

    $stmt = $pdo->prepare("
        INSERT INTO dms (unread_status, message_content, message_image, CreatedDate, user1_id, user2_id)
        VALUES (1, :message_content, :message_image, NOW(), :user_id, :other_user_id)
    ");

    $stmt->bindParam(':message_content', $message_content, PDO::PARAM_STR);
    $stmt->bindParam(':message_image', $message_image, PDO::PARAM_LOB);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        die(" INSERT Failed: " . print_r($stmt->errorInfo(), true));
    } else {
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}


$stmt = $pdo->prepare("
    SELECT dms.*, 
           user1.user_name AS user1_name, 
           user2.user_name AS user2_name
    FROM dms
    JOIN users user1 ON user1.id = dms.user1_id
    JOIN users user2 ON user2.id = dms.user2_id
    WHERE 
       (dms.user1_id = :user1 AND dms.user2_id = :user2)
    OR (dms.user1_id = :user2 AND dms.user2_id = :user1)
    ORDER BY dms.CreatedDate ASC
");

$parameters = [
    ':user1' => $user_id,
    ':user2' => $other_user_id
];

$stmt->execute($parameters);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Chat Container */
        .chat-room {
            overflow: hidden;
            width: 80%;
            margin: 0 auto;
        }

        .chat-messages {
            max-width: 50%;
            height: 500px;
            overflow-y: auto;
            /* border-radius: 0 10px 10px; */
            padding: 0 15px 15px;
            background: #282a36;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            margin: 0 auto;
        }

        /* Custom Scrollbar */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #3b3f5a;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgb(0, 7, 53);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgb(113, 118, 134);
        }

        /* Chat Bubbles */
        .chat-messages p {
            max-width: 80%;
            padding: 10px;
            border-radius: 15px;
            margin: 8px 0;
            font-size: 14px;
            word-wrap: break-word;
        }

        .card-header {
            background-color: rgb(0, 28, 58);
            max-width: 50%;
            padding: 5px 15px;
            border-radius: 10px 10px 0 0;
            margin: 0 auto;
            text-align: center;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-weight: bold;
            color: #cccccc;
        }

        /* Sender's Messages (Your Messages) */
        .sent-chat {
            align-self: flex-end;
            display: flex;
            flex-direction: column;
        }

        .chat-messages #your-message {
            align-self: flex-end;
            background-color: rgb(0, 28, 58);
            color: white;
            cursor: pointer;
            margin: 0 0 5px 0;
        }

        /* Initially hide the message date */
        .message-date {
            display: none;
        }

        #sender-name {
            align-self: flex-end;
            margin: 0;
            color: #cccccc;
            padding: 0 10px;
        }

        .chat-messages #your-message-date {
            align-self: flex-end;
            padding: 0;
            margin: 0;
            color: rgb(206, 206, 206);
        }

        /* Receiver's Messages */
        .recieved-chat {
            align-self: flex-start;
            display: flex;
            flex-direction: column;
            margin: 10px 0 0;
            cursor: pointer;

        }

        .chat-messages #his-message {
            align-self: flex-start;
            background-color: #44475a;
            color: white;
            margin: 0 0 5px 0;
        }

        #reciever-name {
            align-self: flex-start;
            margin: 0 5px 0 0;
            color: #cccccc;
            padding: 0 8px;
        }

        .chat-messages #his-message-date {
            align-self: flex-start;
            padding: 0;
            margin: 0;
            color: rgb(206, 206, 206);
        }

        /* Image Styling */
        .message-img {
            align-self: flex-end;
            max-width: 20%;
            height: auto;
            border-radius: 10px;
            margin-top: 5px;
        }

        /* Chat Input Form */
        .m2m {
            max-width: 50%;
            background: #282a36;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }

        /* Textarea */
        textarea {
            width: 95%;
            height: 80px;
            padding: 10px 10px 0;
            border-radius: 10px;
            border: none;
            resize: none;
            background-color: #3b3f5a;
            color: #fff;
            font-size: 14px;
            margin: 0 auto;
        }

        /* File Input */
        .file-input-wrapper {
            width: 100%;
            margin: auto;
        }

        input[type="file"] {
            display: none;
        }

        /* Custom file upload button */
        .file-upload-label {
            background: #3b3f5a;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
            margin: 10px 0 0;
        }

        .file-upload-label:hover {
            background: #50576e;
        }

        /* Display selected file name */
        #file-name {
            color: #ccc;
            font-size: 14px;
        }

        /* Buttons */
        .btn-secondary,
        .btn-primary {
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-primary {
            background: #007bff;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: rgb(113, 113, 113);
            text-align: center;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: rgb(71, 71, 71);

        }

        /* Responsive Design */
        @media screen and (max-width: 968px) {
            .chat-room {
                overflow: hidden;
                width: 90%;
                margin: 0 auto;
            }

            .card-header {
                background-color: rgb(0, 28, 58);
                max-width: 80%;
                padding: 5px 15px;
                border-radius: 10px 10px 0 0;
                margin: 0 auto;
                text-align: center;
                font-family: Verdana, Geneva, Tahoma, sans-serif;
                font-weight: bold;
                color: #cccccc;
            }

            .chat-messages {
                max-width: 80%;
                height: 450px;
                overflow-y: auto;
                /* border-radius: 0 10px 10px; */
                padding: 0 15px 15px;
                background: #282a36;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                margin: 0 auto;
            }

            .m2m {
                max-width: 80%;
                background: #282a36;
                padding: 15px;
                border-radius: 0 0 10px 10px;
                display: flex;
                flex-direction: column;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                margin: 0 auto;
            }

        }

        @media screen and (max-width: 768px) {
            .chat-room {
                overflow: hidden;
                width: 90%;
                margin: 0 auto;
            }

            .card-header {
                background-color: rgb(0, 28, 58);
                max-width: 90%;
                padding: 5px 15px;
                border-radius: 10px 10px 0 0;
                margin: 0 auto;
                text-align: center;
                font-family: Verdana, Geneva, Tahoma, sans-serif;
                font-weight: bold;
                color: #cccccc;
            }

            .chat-messages {
                max-width: 100%;
                height: 400px;
                overflow-y: auto;
                /* border-radius: 0 10px 10px; */
                padding: 0 15px 15px;
                background: #282a36;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                margin: 0 auto;
            }

            .m2m {
                max-width: 100%;
                background: #282a36;
                padding: 15px;
                border-radius: 0 0 10px 10px;
                display: flex;
                flex-direction: column;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                margin: 0 auto;
            }


        }
    </style>
</head>

<body>
    <?php require "navbar.php" ?>
    <div class="chat-room">
        <div class="card-header">
            <p>Chat with <?php echo htmlspecialchars($other_user_name); ?></p>
        </div>
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
                            <p id="your-message-date" class="message-date"><?php echo "&nbsp" . htmlspecialchars($msg['CreatedDate']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($msg['message_image'])): ?>
                            <img class="message-img" src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="400">
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
                            <p id="his-message-date" class="message-date"><?php echo "&nbsp" . htmlspecialchars($msg['CreatedDate']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($msg['message_image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="150">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>


        <form class="m2m" method="POST" enctype="multipart/form-data">
            <div class="file-input-wrapper">
                <textarea id="messageContent" name="message" rows="4" placeholder="Skriv ditt inlägg här..."></textarea>
                <!-- Updated File Input in Form -->
                <label for="message_image" class="file-upload-label">Choose a file</label>
                <span id="file-name">No file chosen</span>
                <input type="file" id="message_image" name="message_image">
            </div>
            <button type="submit" name="save" class="btn btn-primary">Send</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
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

        window.onload = function() {
            scrollToBottom();
        };

        document.querySelector("form").addEventListener("submit", function() {
            setTimeout(scrollToBottom, 100);
        });
        document.getElementById("message_image").addEventListener("change", function() {
            var fileName = this.files[0] ? this.files[0].name : "No file chosen";
            document.getElementById("file-name").textContent = fileName;
        });
    </script>
</body>

</html>