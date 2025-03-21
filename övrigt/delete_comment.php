<?php
require_once '../Entity/userEntity.php';
session_start();
require_once("../övrigt/PDO.php");
require_once "../Dao/postsDAO.php";
require_once '../config.php';
$source = $_POST['source'] ?? '/Views/blogwall.php';

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

header("Location: " . BASE_URL . $source);
exit;
