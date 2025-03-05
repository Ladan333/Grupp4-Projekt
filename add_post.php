<?php
session_start();
require 'PDO.php'; 


if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $content = $_POST['content'] ?? '';
    $title = $_POST['title'] ?? 'Untitled';
    $imageBase64 = null;

    
    if (empty($content)) {
        $_SESSION['error'] = 'Content cannot be empty.';
        header("Location: blogwall.php");
        exit();
    }
    
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageBase64 = base64_encode($imageData);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO blogposts (title, blogContent, image_base64, user_id) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $imageBase64, $_SESSION['id']]);

        
        $_SESSION['success'] = 'Post added successfully!';
        header("Location: blogwall.php");
        exit();

    } catch (PDOException $e) {
        
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: blogwall.php");
        exit();
    }
}
?>
