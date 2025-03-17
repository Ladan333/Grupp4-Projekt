<?php
session_start(); 
require 'PDO.php';
require_once "userDAO.php";
require_once 'userEntity.php';

if ($_SESSION['role'] != 1) {  
    die("Unauthorized access!");
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = $_GET['id'];


    $isAdmin = (isset($_GET['role']) && $_GET['role'] == 0) ? 1 : 0;

    $userDAO = new UserDAO($pdo);
    $userDAO->changeRole($isAdmin, $user_id);

   
    header("Location: admin_list.php");
    exit;
} else {
    echo "Invalid request. User ID is missing.";
    exit;
}