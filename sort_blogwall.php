<?php

require_once "userEntity.php";
if (session_status() == PHP_SESSION_NONE) session_start();
require 'PDO.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sort_recent"])) {
    $_SESSION['sorting'] = 1;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['sort_comment_count'])) {
    $_SESSION['sorting'] = 2;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['sort_activity'])) {
    $_SESSION['sorting'] = 3;
}

header("Location: blogwall.php");
exit();