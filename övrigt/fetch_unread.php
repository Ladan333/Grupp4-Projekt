<?php


require_once "userEntity.php";
session_start();
require_once('PDO.php');
require_once "DmDAO.php";
if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
    $user_id = $user->getId();
}

if ($user_id) {

    $dmDao = new DmDAO($pdo);
    $fetchcount = $dmDao->fetchCountDm($user_id);

    echo json_encode(['unread_count' => $fetchcount['unread_count']]);
} else {
    echo json_encode(['unread_count' => 0]);
}
exit;




?>