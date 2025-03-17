<?php
require "PDO.php";
require_once "PostsDAO.php";
require_once "userEntity.php";

session_start();

$source = $_SESSION['last_page'];

if ($_SERVER['REQUEST_METHOD'] && isset($_POST)) {
    $id = $_POST['post_id'];
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    
    
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $image = base64_encode($imageData);
    }else{
        $PostDao = new PostsDAO($pdo);
        $image = $PostDao->getPic($id);
    }
    $PostDao = new PostsDAO($pdo);
    $PostDao->updatepost($id, $title, $content, $image);
    
    header("Location: $source");
    exit();

}



