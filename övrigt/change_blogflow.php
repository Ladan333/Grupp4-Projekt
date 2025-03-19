<?php

require_once "../Entity/userEntity.php";
if (session_status() == PHP_SESSION_NONE)
    session_start();

require '../övrigt/PDO.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../Views/index.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_view"])) {

    if (empty($_SESSION['blogflow']) || $_SESSION['blogflow'] == 1) {
        $_SESSION['blogflow'] = 2;
    } else if (empty($_SESSION['blogflow']) || $_SESSION['blogflow'] == 2) {
        $_SESSION['blogflow'] = 1;
    }

    header("Location: ../Views/blogwall.php");
    exit();
}

header("Location: ../Views/blogwall.php");
exit();