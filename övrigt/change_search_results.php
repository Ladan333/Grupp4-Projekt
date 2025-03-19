<?php

require_once "../Entity/userEntity.php";
if (session_status() == PHP_SESSION_NONE)
    session_start();

require '../övrigt/PDO.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../Views/index.php");
    exit();
}

$search = "Location: ../Views/search.php?search=" . $_SESSION['search'] . "&search_sort=" . $_SESSION['search_sort'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_view"])) {

    if (empty($_SESSION['search_sort']) || $_SESSION['search_sort'] == 1) {
        $_SESSION['search_sort'] = 2;
    } else if (empty($_SESSION['search_sort']) || $_SESSION['search_sort'] == 2) {
        $_SESSION['search_sort'] = 1;
    }

    header($search);
    exit();
}




header($search);
exit();