<?php
require_once '../Entity/userEntity.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "../övrigt/PDO.php";
require_once "../Dao/DmDAO.php";
require_once "../Dao/UserDAO.php";



if (isset($_SESSION["user"]) && $_SESSION["user"] instanceof User) {
    $user = $_SESSION['user'];
    $user_id = $user->getId();
} else {
    // Hantera fallet när sessionen inte har ett korrekt User-objekt
    echo "User session is not valid.";
}

// Displays the amount of messages the user has that are marked as unread
if ($user_id) {
    $dmDao = new DmDAO($pdo);
    $fetchcount = $dmDao->displayDmCount($user_id);

    $_SESSION['display_count'] = $fetchcount['unread_count'];

} else {
    $_SESSION['display_count'] = 0;
}
?>