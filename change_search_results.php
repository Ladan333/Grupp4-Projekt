<?php

if (session_status() == PHP_SESSION_NONE) session_start();

require 'PDO.php'; 

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$search = "Location: search.php?search=" . $_SESSION['search'] . "&search_sort=" . $_SESSION['search_sort'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_view"])) {

    if (empty($_SESSION['search_sort']) || $_SESSION['search_sort'] == 1) {
        $_SESSION['search_sort'] = 2; 
    } else if(empty($_SESSION['search_sort']) || $_SESSION['search_sort'] == 2){
        $_SESSION['search_sort'] = 1; 
    } 

    header($search);
    exit();
}




header($search);
exit();