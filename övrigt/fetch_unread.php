<?php


require_once "../Entity/userEntity.php";
session_start();
require_once('../övrigt/PDO.php');
require_once "../Dao/DmDAO.php";

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $user_id = $user->getId();

}

// Fetches all messages and checks how many that are marked as unread
if ($user_id) {
    //Get unread messages for messages
    $dmDao = new DmDAO($pdo);
    $fetchcount = $dmDao->fetchCountDm($user_id);

    echo json_encode(['unread_count' => $fetchcount['unread_count']]);
} else {
    echo json_encode(['unread_count' => 0]);
}

exit;



?>