<?php
session_start();
require_once("PDO.php");
require_once("UserDAO.php");
require_once ('UserController.php');
require_once "userEntity.php";



$user_id = $_POST['id'];

$username = new UserController($pdo);
$username->adminDeleteUser($user_id);
