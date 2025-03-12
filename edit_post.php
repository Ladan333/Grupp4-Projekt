<?php
require "PDO.php";
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
        $stmt = $pdo->prepare("SELECT image_base64 FROM blogposts where id = :id"); 
        $stmt->bindParam(":id", $id); 
        $stmt->execute(); 
        $image = $stmt->fetch(PDO::FETCH_COLUMN);
    }
    

    $stmt = $pdo->prepare("UPDATE blogposts SET title = :title, blogContent = :content, image_base64 = :image_base64  WHERE id = :id ");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $stmt->bindParam(":content", $content, PDO::PARAM_STR);
    $stmt->bindParam(":image_base64", $image, PDO::PARAM_STR);
    $stmt->execute();

    header("Location: $source");
    exit();

}



