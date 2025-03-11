<?php
session_start();
require_once("PDO.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_comment"])) {
    $id = $_POST["delete_comment"];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Comment deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete comment.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header("Location: blogwall.php");
exit;
