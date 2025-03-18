<?php
require_once 'userEntity.php';
session_start();
require_once("PDO.php");
require_once "postsDAO.php";
$source = $_SESSION['last_page'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_comment"])) {
    $id = $_POST["delete_comment"];
    $postsDAO = new PostsDAO($pdo);
    try {
        $deleteComment = $postsDAO->deleteComments($id);
        
        if ($deleteComment) {
            $_SESSION['message'] = "Comment deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete comment.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header("Location: $source");
exit;
