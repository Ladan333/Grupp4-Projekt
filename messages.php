<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('PDO.php');
session_start();


if (!isset($_SESSION['id'])) {
    die("❌ ERROR: No user logged in.");
}

$user_id = (int) $_SESSION['id'];

$conversations = [];


$stmt = $pdo->prepare("
    SELECT dms.id AS message_id,
           dms.message_content,
           dms.CreatedDate,
           dms.unread_status,
           dms.user1_id,
           dms.user2_id,
           CASE 
               WHEN dms.user1_id = :user_id THEN user2.user_name 
               ELSE user1.user_name 
           END AS conversation_partner
    FROM dms
    JOIN users user1 ON user1.id = dms.user1_id
    JOIN users user2 ON user2.id = dms.user2_id
    WHERE dms.id IN (
        SELECT MAX(id) FROM dms 
        WHERE user1_id = :user_id1 OR user2_id = :user_id2
        GROUP BY LEAST(user1_id, user2_id), GREATEST(user1_id, user2_id)
    )
    ORDER BY dms.CreatedDate DESC
");

$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


$unreadStmt = $pdo->prepare("
    SELECT COUNT(DISTINCT user1_id) AS unread_count 
    FROM dms 
    WHERE unread_status = 1 
    AND user2_id = :user_id
");
$unreadStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$unreadStmt->execute();
$unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <title>Messenger</title>
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="messenger">
        <h2>Your Conversations</h2>
        <p>Unread Messages: <strong><?= $unreadCount ?></strong></p>

        <ul class="messages-list">
            <?php foreach ($messages as $msg): ?>
                <li class="message-preview">
                    <a href="m2m.php?user_name=<?= urlencode($msg['conversation_partner']) ?>">
                        <strong><?= htmlspecialchars($msg['conversation_partner']) ?></strong>
                        <br>
                        <span class="message-text"><?= htmlspecialchars($msg['message_content']) ?></span>
                        <br>
                        <span class="message-date"><?= htmlspecialchars($msg['CreatedDate']) ?></span>
                        <?php if ($msg['unread_status'] == 1): ?>
                            <span class="unread-indicator">🔴</span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

</body>

</html>
