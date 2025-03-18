<?php 
require_once '../Entity/userEntity.php';
session_start(); 
require_once("../övrigt/PDO.php");
require_once "../övrigt/followDAO.php";

if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
    $user_id = $user->getId();
}else{
    header('Location: index.php');
} // Current logged-in user

$followDao = new FollowDAO($pdo);
$result = $followDao->showFollowers($user_id);

if(!$result){
    header("Location: profile.php");
}



?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS.css">
    <title>Document</title>
</head>
<body>
<?php require "navbar.php"?>     

<ul class="searching-list">
<?php foreach ($result as $row): ?>
    <?php $profile_img = !empty($row['profile_image']) ? "data:image/png;base64," . htmlspecialchars($row['profile_image']) : "./files/no_picture.jpg"; ?>
    <li class="searchResult">
        <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="50" height="50">
        <a href="profile.php?user_name=<?php echo urlencode($row['user_name']); ?>" class="profile-link">

            <span
                class="name"><?php echo htmlspecialchars($row["first_name"]) . " " . htmlspecialchars($row["last_name"]); ?></span>

            <span class="username-search"><?php echo htmlspecialchars($row["user_name"]); ?></span>
        </a>
    </li>
    <!-- <?php var_dump($row); ?> -->
<?php endforeach; ?>


</>

</body>
</html>