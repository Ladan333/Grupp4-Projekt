<?php
session_start();
require 'PDO.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Ej inloggad"]);
    exit();
}

$user_id = $_SESSION['id'];
$post_id = $_POST['post_id'] ?? null;

if ($post_id) {
    // Kolla om användaren redan har gillat inlägget
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id");
    $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    $like = $stmt->fetch();

    if ($like) {
        // Ta bort gillningen om den redan finns
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    } else {
        // Lägg till en gillning
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    }

    // Hämta det nya antalet gillningar
    $stmt = $pdo->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    $like_count = $stmt->fetchColumn();

    echo json_encode(["success" => true, "likes" => $like_count]);
    exit();
}

echo json_encode(["success" => false, "message" => "Ogiltigt post_id"]);
exit();
