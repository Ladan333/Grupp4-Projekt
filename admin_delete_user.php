<?php
session_start();
require 'PDO.php';
require 'UserController';



$user_id = $_POST['id'];

$username = new UserController($pdo);
$username->adminDeleteUser($user_id);
