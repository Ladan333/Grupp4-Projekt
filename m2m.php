<?php
require_once('PDO.php');
session_start();
$user_id = $_SESSION['id']; 
$other_user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : null;

if (!$other_user_name) {
    die("Invalid request: Missing username.");
}




$stmt = $pdo->prepare("SELECT id FROM users WHERE user_name = :user_name");
$stmt->bindParam(':user_name', $other_user_name, PDO::PARAM_STR);
$stmt->execute();
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    die("User not found.");
}

$other_user_id = $other_user['id'];


$updateStmt = $pdo->prepare("
    UPDATE dms 
    SET unread_status = 0 
    WHERE unread_status = 1 
    AND user1_id = :other_user_id 
    AND user2_id = :user_id
");

$updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$updateStmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
$updateStmt->execute();



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save'])) {
    $user1_id = $_SESSION['id']; 
    $user2_id = $other_user_id; 

    $message_content = !empty($_POST['message']) ? trim($_POST['message']) : NULL;
    $message_image = NULL;

    if (!empty($_FILES['message_image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['message_image']['tmp_name']);
        $message_image = base64_encode($imageData);
    }


    $stmt = $pdo->prepare("
        INSERT INTO dms (unread_status, message_content, message_image, CreatedDate, user1_id, user2_id)
        VALUES (TRUE, :message_content, :message_image, NOW(), :user1_id, :user2_id)
    ");

    $stmt->bindParam(':message_content', $message_content, PDO::PARAM_STR);
    $stmt->bindParam(':message_image', $message_image, PDO::PARAM_LOB);
    $stmt->bindParam(':user1_id', $user1_id, PDO::PARAM_INT);
    $stmt->bindParam(':user2_id', $user2_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['REQUEST_URI']); 
        exit();
    } else {
        echo "Error sending message.";
    } 
}

$user_id = $_SESSION['id']; 


$stmt = $pdo->prepare("
    SELECT dms.*, 
           user1.user_name AS user1_name, 
           user2.user_name AS user2_name
    FROM dms
    JOIN users user1 ON user1.id = dms.user1_id
    JOIN users user2 ON user2.id = dms.user2_id
    WHERE (dms.user1_id = :user_id AND dms.user2_id = :other_user_id)
       OR (dms.user1_id = :other_user_id AND dms.user2_id = :user_id)
    ORDER BY dms.CreatedDate ASC
    
");

$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
    function scrollToBottom() {
        var chatMessages = document.querySelector(".chat-messages");
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

   
    window.onload = function() {
        scrollToBottom();
    };

    document.querySelector("form").addEventListener("submit", function() {
        setTimeout(scrollToBottom, 100); 
    });
</script>

<?php require "navbar.php"?>
<div class="chat-messages">
    <?php foreach ($messages as $msg): ?>
        <?php if ($msg['user1_id'] == $user_id): ?>
            <p><strong>Du:</strong> <?php echo htmlspecialchars($msg['message_content']) . "&nbsp". htmlspecialchars($msg['CreatedDate']);?></p>
            <?php if (!empty($msg['message_image'])): ?>
                <img class="message-img" src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="400">
            <?php endif; ?>
        <?php else: ?>
            <p><strong><?php echo htmlspecialchars($msg['user1_name']); ?>:</strong>
                <?php echo htmlspecialchars($msg['message_content']); ?>
            </p>
            <?php if (!empty($msg['message_image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($msg['message_image']); ?>" width="150">
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>


<form method="POST" enctype="multipart/form-data">
    <textarea id="messageContent" name="message" rows="4" placeholder="Skriv ditt inlägg här..."></textarea>
    <div class="mb-3">
        <label for="message_image" class="form-label"> Skicka bild</label>
        <input type="file" id="message_image" name="message_image" class="form-control bg-dark text-light">
    </div>
    <button type="submit" name="save" class="btn btn-primary">Save Changes</button>
    <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>