<?php
require_once '../Entity/userEntity.php';
session_start();
require '../övrigt/PDO.php';
require_once '../Controller/UserController.php';
require_once '../Dao/UserDAO.php';
require_once '../config.php';
//Delete post - ligger i blogwall
//Tar in post-id och kör query från metod i UserDao.php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])){
    $post_id = $_POST['post_id'];
    
    $do = new UserDAO($pdo);
    $success = $do->DeleteBlogPostBy($post_id);// returnerar true om query körs


if($success){
    $_SESSION['success'] = 'Post deleted successfully!';
    header("Location: ../Views/blogwall.php ");
    exit();
}

else{
    $_SESSION['error'] = "You dont have permission to delete this post";
    header("Location: ../Views/blogwall.php");
    exit();
}

}

//Delete user - ligger i edituser.php
//Tar in userid från POST i edituser.php kör deletequery som ligger i metod i UserDao
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['deletes'])){
     $user = (int)$_POST['deletes'];

     if(!empty($user)){
        //använder metod i UserDao
          $do = new UserDAO($pdo);
          $do->DeleteUserById($user);
        

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


//Delete comment - ligger i blogwall rad 311
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-comment'])){
    $deleteComment = $POST['delete-comment'];

    $do->DeleteCommentsByID($deleteComment);
  
    
    $source = $_POST['source'] ?? '/Views/blogwall.php';
    if (!empty($_POST['delete-comment'])) {
        $query->execute();
        header("Location: " . BASE_URL . $source);
        exit();
    } else {
        $_SESSION['error'] = "Failed";
    }

}
