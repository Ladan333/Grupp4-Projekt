<?php
session_start();
require_once("../övrigt/PDO.php");
require_once("../Dao/UserDAO.php");
require_once ('../Controller/UserController.php');
require_once "../Entity/userEntity.php";
require_once '../config.php';


$user_id = $_POST['id'];

$username = new UserController($pdo);
$username->adminDeleteUser($user_id);
