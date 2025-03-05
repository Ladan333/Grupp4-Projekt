<?php
session_start();
require 'PDO.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if post ID is provided
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['id']; // Get the logged-in user ID

    // Fetch the post to check if it belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT user_id FROM blogposts WHERE id = :post_id");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Check if the logged-in user is the owner of the post or if they are an admin
        if ($post['user_id'] == $user_id || $_SESSION['role'] == 'admin') {
            // Delete the post from the database
            $deleteStmt = $pdo->prepare("DELETE FROM blogposts WHERE id = :post_id");
            $deleteStmt->bindParam(':post_id', $post_id);
            $deleteStmt->execute();

            // Redirect to the blog wall with a success message
            $_SESSION['success'] = 'Post deleted successfully!';
            header("Location: blogwall.php");
            exit();
        } else {
            // If the user is not the owner and not an admin, deny access
            $_SESSION['error'] = 'You do not have permission to delete this post.';
            header("Location: blogwall.php");
            exit();
        }
    } else {
        // If the post does not exist, show an error
        $_SESSION['error'] = 'Post not found.';
        header("Location: blogwall.php");
        exit();
    }
} else {
    // If no post ID is provided, redirect with an error message
    $_SESSION['error'] = 'Invalid post ID.';
    header("Location: blogwall.php");
    exit();
}
?>
