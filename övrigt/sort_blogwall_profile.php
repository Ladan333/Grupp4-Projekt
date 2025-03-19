<?php

require_once "../Entity/userEntity.php";
if (session_status() == PHP_SESSION_NONE) session_start();
require '../övrigt/PDO.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: ../Views/index.php");
    exit();
}

// Changes the sessions sorting variable to match the selected sorting option
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sort_recent"])) {
    $_SESSION['sorting'] = 1;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['sort_comment_count'])) {
    $_SESSION['sorting'] = 2;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['sort_activity'])) {
    $_SESSION['sorting'] = 3;
}

$location = "Location: ../Views/profile.php?user_name=" . $_SESSION['follow_username'];

header($location);
exit();