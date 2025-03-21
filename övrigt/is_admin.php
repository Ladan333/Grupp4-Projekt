<?php
require_once '../Entity/userEntity.php';
session_start();
require '../övrigt/PDO.php';
require_once "../Dao/userDAO.php";

if ($_SESSION['role'] != 1) {
    die("Unauthorized access!");
}

// Changes the role of the selected user if the current user is an admin
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = $_GET['id'];


    $isAdmin = (isset($_GET['role']) && $_GET['role'] == 0) ? 1 : 0;
    //update new role
    $userDAO = new UserDAO($pdo);
    $userDAO->changeRole($isAdmin, $user_id);


    header("Location: ../Views/admin_list.php");
    exit;
} else {
    echo "Invalid request. User ID is missing.";
    exit;
}