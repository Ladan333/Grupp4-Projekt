<?php

require_once "userEntity.php";
session_start();
require 'PDO.php';
require 'FollowDAO.php';  

if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
    $user_id = $user->getId();
}else{
    header('Location: index.php');
    exit;
}


$profile_id = $_SESSION['profile_id'];

$followDao = new FollowDAO($pdo);


$results = $followDao->getallFollows($id, $profile_id);

if ($_SESSION['profile_id'] != $user_id && empty($results)) {
   
    if ($followDao->follow($user_id, $profile_id)) {
        echo "You are now following this user!";
    } else {
        echo "Failed to follow the user!";
    }
} else if ($_GET['user_id'] != $user_id && !empty($results)) {
    
    if ($followDao->unfollow($id, $profile_id)) {
        echo "You have unfollowed this user.";
    } else {
        echo "Failed to unfollow the user!";
    }
}

$location = "Location: profile.php?user_name=" . $_SESSION['follow_username'];

header($location);
exit();

?>