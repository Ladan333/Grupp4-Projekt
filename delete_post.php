<?php
session_start();
require 'PDO.php';


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])){
    $post_id = $_POST['post_id'];

$stmt = $pdo->prepare("DELETE FROM blogposts WHERE id = :post_id ");
$stmt->bindParam(':post_id', $post_id);

$stmt->execute();
$_SESSION['success'] = 'Post deleted successfully!';
header("Location: blogwall.php ");
exit();

}

else{
    $_SESSION['error'] = "You dont have permission to delete this post";
    header("Location: blogwall.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['delete'])){
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :delete-user");
    $stmt->bindParam(':delete-user', $_POST['id']);
    $stmt->execute();


    header('Location: index.php');
}
else{
    $_SESSION[.error]
}






// session_start();
// require 'PDO.php'; 


// if (!isset($_SESSION['username'])) {
//     header("Location: index.php");
//     exit();
// }


// if (isset($_POST['post_id'])) {
//     $post_id = $_POST['post_id'];
//     $user_id = $_SESSION['id']; 

    
//     $stmt = $pdo->prepare("SELECT user_id FROM blogposts WHERE id = :post_id");
//     $stmt->bindParam(':post_id', $post_id);
//     $stmt->execute();
//     $post = $stmt->fetch(PDO::FETCH_ASSOC);

//     if ($post) {
        
//         if ($post['user_id'] == $user_id || $_SESSION['role'] == 'admin') {
            
//             $deleteStmt = $pdo->prepare("DELETE FROM blogposts WHERE id = :post_id");
//             $deleteStmt->bindParam(':post_id', $post_id);
//             $deleteStmt->execute();

            
//             $_SESSION['success'] = 'Post deleted successfully!';
//             header("Location: blogwall.php");
//             exit();
//         } else {
            
//             $_SESSION['error'] = 'You do not have permission to delete this post.';
//             header("Location: blogwall.php");
//             exit();
//         }
//     } else {
        
//         $_SESSION['error'] = 'Post not found.';
//         header("Location: blogwall.php");
//         exit();
//     }
// } else {
    
//     $_SESSION['error'] = 'Invalid post ID.';
//     header("Location: blogwall.php");
//     exit();
// }

