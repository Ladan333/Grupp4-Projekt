<?php

session_start();
require 'PDO.php'; 


if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo ->prepare("SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id");
$stmt->bindParam(":user_id", $_SESSION['id']);
$stmt->bindParam(':follow_id', $_GET['id']);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// $sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, bp.CreatedDate, bp.image_base64, bp.user_id
//         FROM blogposts bp
//         JOIN users u ON bp.user_id = u.id
//         WHERE u.id = :id
//         ORDER BY bp.CreatedDate DESC";
// $stmt = $pdo->prepare($sql);
// $stmt->bindParam(":id", $_GET['id']);
// $stmt->execute();
// $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($posts);

if (isset($_GET['id'])) {
    if ($_GET['id'] != $_SESSION['id'] && empty($results)) {
        $stmt = $pdo -> prepare('INSERT INTO follows (user_id, follow_id) VALUES (:user_id, :follow_id)');
        $stmt->bindParam(":user_id", $_SESSION['id']);
        $stmt->bindParam(':follow_id', $_GET['id']);
        $stmt->execute();
    }
    else if ($_GET['user_id'] != $_SESSION['id'] && !empty($results)) {
        $stmt = $pdo -> prepare('DELETE FROM follows WHERE user_id = :user_id AND follow_id = :follow_id');
        $stmt->bindParam(":user_id", $_SESSION['id']);
        $stmt->bindParam(':follow_id', $_GET['id']);
        $stmt->execute();
    }
}

// header("Location: profile.php");
// exit();

?>