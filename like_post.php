<?php
session_start();
require 'PDO.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
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
}

// Skicka tillbaka användaren till föregående sida
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
