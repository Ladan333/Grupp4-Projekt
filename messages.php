<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('PDO.php');
require_once "DmDAO.php";
session_start();


if (!isset($_SESSION['id'])) {
    header('Location: index.php');
}

$user_id = (int) $_SESSION['id'];

$conversations = [];

$dmDao = new DmDAO($pdo);
$messages = $dmDao->getMessages($user_id);

$_SESSION['display_count'] = $unreadCount ?? 0;


$dmDao = new DmDAO($pdo);
$unreadCount = $dmDao->unreadMessages($user_id);

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




    <ul class="searching-list">
        <?php foreach ($messages as $msg): ?>
            <li class="searchResult">
                <?php
                $profile_img = !empty($msg['profile_image'])
                    ? "data:image/png;base64," . htmlspecialchars($msg['profile_image'], ENT_QUOTES, 'UTF-8')
                    : "./files/no_picture.jpg";
                ?>
                <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="50" height="50">
                <a href="m2m.php?user_name=<?= urlencode($msg['conversation_partner'] ?? '') ?>">
                    <strong><?= htmlspecialchars($msg['conversation_partner'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                    <br>
                    <span class="name"><?= htmlspecialchars($msg['message_content'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <br>
                    <!-- <span class="message-date"><?= htmlspecialchars($msg['CreatedDate'] ?? '', ENT_QUOTES, 'UTF-8') ?></span> -->
                    <?php if (!empty($msg['unread_status']) && $msg['unread_status'] == 1 && $msg['user2_id'] == $user_id): ?>
                        <span class="unread-indicator" style="color:white;">Unread</span>
                    <?php endif; ?>

                </a>
            </li>
        <?php endforeach; ?>

    </ul>



</body>

</html>