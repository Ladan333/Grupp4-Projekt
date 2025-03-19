<?php
require "../övrigt/PDO.php";
require_once "../Dao/PostsDAO.php";
require_once '../Entity/userEntity.php';

session_start();

$source = isset($_POST['source']) ? '/Grupp4-Projekt/Views/' . basename($_POST['source']) : '/Grupp4-Projekt/Views/blogwall.php';

if ($_SERVER['REQUEST_METHOD'] && isset($_POST)) {
    $id = $_POST['post_id'];
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);


    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $image = base64_encode($imageData);
    } else {
        $PostDao = new PostsDAO($pdo);
        $image = $PostDao->getPic($id);
    }
    //Edit posts
    $PostDao = new PostsDAO($pdo);
    $PostDao->updatepost($id, $title, $content, $image);

    header("Location: $source");
    exit();

}



