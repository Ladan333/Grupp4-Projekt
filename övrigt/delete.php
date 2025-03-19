<?php
require_once '../Entity/userEntity.php';
session_start();
require '../övrigt/PDO.php';
require_once '../Controller/UserController.php';
require_once '../Dao/UserDAO.php';
require_once '../config.php';
//Delete post - ligger i blogwall
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    $stmt = $pdo->prepare("DELETE FROM blogposts WHERE id = :post_id ");
    $stmt->bindParam(':post_id', $post_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Post deleted successfully!';
        header("Location: ../Views/blogwall.php ");
        exit();
    } else {
        $_SESSION['error'] = "You dont have permission to delete this post";
        header("Location: ../Views/blogwall.php");
        exit();
    }

}

//Delete user - ligger i edituser.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletes'])) {
    $user = (int) $_POST['deletes'];

    if (!empty($user)) {

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :deleteuser");
        $stmt->bindParam(':deleteuser', $user, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $_SESSION['success'] = 'User deleted succesful';
            unset($_SESSION[""]);
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
            header("Location: ../Views/index.php");

            exit();
        } else {
            $_SESSION["error"] = "Failed";
        }
    } else {
        $_SESSION["error"] = "Invalid username";
        header("edituser.php");
        exit();
    }


}

//Delete comment - ligger i blogwall rad 298
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-comment'])) {
    $query = $pdo->prepare('DELETE FROM comments WHERE id = :userid');
    $query->bindParam(':userid', $_POST['delete-comment']);

    $source = $_POST['source'] ?? '/Views/blogwall.php';
    if (!empty($_POST['delete-comment'])) {
        $query->execute();
        header("Location: " . BASE_URL . $source);
        exit();
    } else {
        $_SESSION['error'] = "Failed";
    }

}
