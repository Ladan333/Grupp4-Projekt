<?php
require '../Entity/userEntity.php';
session_start();
require '../övrigt/PDO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user']->getId();

    // Kontrollera om användaren redan har gillat inlägget
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id");
    $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    $like = $stmt->fetch();

    if ($like) {
        // Ta bort gilla
        $stmt = $pdo->prepare("DELETE FROM likes WHERE id = :id");
        $stmt->execute(['id' => $like['id']]);
    } else {
        // Lägg till gilla
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    }

    // Hämta det uppdaterade antalet gillningar
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    $like_count = $stmt->fetchColumn();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'like_count' => $like_count]);
    exit();
}