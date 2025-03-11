<?php
require "PDO.php";
session_start();


if ($_SERVER['REQUEST_METHOD'] && isset($_POST)) {
    $id = $_POST['post_id'];
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    $stmt = $pdo->prepare("UPDATE blogposts SET title = :title, blogContent = :content WHERE id = :id ");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $stmt->bindParam(":content", $content, PDO::PARAM_STR);
    $stmt->execute();

    header("Location: blogwall.php");
    exit();

}



