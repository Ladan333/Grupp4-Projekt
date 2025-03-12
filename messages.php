<?php

use function PHPSTORM_META\type;

$cookie_name = "user_session";
$cookie_value = session_id();
$cookie_time = time() + 3600;

setcookie($cookie_name, $cookie_value, $cookie_time, "/", "", false, true);


session_start();
require_once 'PDO.php';

if ($_SESSION['id'] == null) {
    header("Location: index.php");
    exit();
}

$conversations = [];

if (!$_SESSION['id'] == null) {
    $stmt = $pdo->prepare('SELECT MAX(id) FROM dms WHERE user1_id = :session_id OR user2_id = :session_id
GROUP BY user1_id');
    $stmt->bindparam(":session_id", $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchall(PDO::FETCH_ASSOC);
}

if ($results != null) { 
    foreach ($results as $row) {
    $stmt = $pdo ->prepare('SELECT dms.id, u.user_name, dms.unread_status, dms.CreatedDate, dms.message_content
                                   FROM dms JOIN users as u ON dms.user1_id = u.id OR dms.user2_id = u.id
                                   WHERE dms.id = :result_id AND u.user_name NOT LIKE :user_name ');
    $stmt -> bindparam(":result_id", $row['MAX(id)'], type: PDO::PARAM_INT);
    $user_name = '%' . $_SESSION['username'] . '%';
    $stmt -> bindparam(':user_name', $user_name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);

    array_push($conversations, $result);
}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="20"> -->
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <link preload href="./files/Leche_Frita.ttf" as="font" type="font/ttf" crossorigin>
    <title>Document</title>
</head>

<body>
<?php require "navbar.php"; ?>


    <main class="index">

    <?php if (!empty($conversations)) { ?>
            <ul>
                <?php foreach ($conversations as $conversation): ?>
                    <?php foreach ($conversation as $key => $value): ?>
                    <?php $profile_img = !empty($row['profile_image']) ? "data:image/png;base64," . htmlspecialchars( $value['profile_image']) : "./files/no_picture.jpg"; ?>
                    <li class="searchResult">
                        <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="50" height="50">
                        <a href="conversations.php?user_name=<?php echo urlencode($value['user_name']); ?>" class="profile-link">

                            <span class="username-search"><?php echo htmlspecialchars($value["user_name"]); ?></span>
                            <?php $message_content = htmlspecialchars($value["message_content"])?>
                            <span class="username-search"><?php echo htmlspecialchars($message_content =  (strlen($message_content) > 75) ? substr($message_content, 0, 75) . '...' : $message_content); ?></span>
                            <?php if ($value['unread_status'] == 1) { ?>
                                <span class="username-search">Unread</span>
                            <?php } ?>
                           
                        </a>
                    </li>
                    <!-- <?php var_dump($row); ?> -->
                    <?php endforeach; ?>
                <?php endforeach; } ?>
                
            

            </ul>



       
    </main>

</body>

</html>