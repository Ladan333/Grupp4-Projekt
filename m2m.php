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

<script>
    function scrollToBottom() {
        var chatMessages = document.querySelector(".chat-messages");
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    window.onload = function () {
        scrollToBottom();
    };

    document.querySelector("form").addEventListener("submit", function () {
        setTimeout(scrollToBottom, 100);
    });
</script>

<?php require "navbar.php"; ?>
<div class="chat-messages">
    <?php foreach ($messages as $msg): ?>
        <p><strong><?= htmlspecialchars($msg['user1_id'] == $user_id ? "Du" : $msg['user1_name']); ?>:</strong>
            <?= htmlspecialchars($msg['message_content']) ?> (<?= htmlspecialchars($msg['CreatedDate']); ?>)
        </p>
        <?php if (!empty($msg['message_image'])): ?>
            <img class="message-img" src="data:image/jpeg;base64,<?= htmlspecialchars($msg['message_image']); ?>" width="400">
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<form method="POST" enctype="multipart/form-data">
    <textarea id="messageContent" name="message" rows="4" placeholder="Skriv ditt meddelande..."></textarea>
    <div class="mb-3">
        <label for="message_image" class="form-label"> Skicka bild</label>
        <input type="file" id="message_image" name="message_image" class="form-control">
    </div>
    <button type="submit" name="save" class="btn btn-primary">Send Message</button>
</form>
