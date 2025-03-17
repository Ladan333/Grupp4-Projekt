<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('PDO.php');
require_once "DmDAO.php";


$user_id = $_SESSION['id'] ?? null;

if ($user_id) {
$dmDao = new DmDAO($pdo);
$fetchcount = $dmDao->displayDmCount($user_id);

    $_SESSION['display_count'] = $fetchcount['unread_count'];
} else {
    $_SESSION['display_count'] = 0;
}
?>