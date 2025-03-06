<?php
session_start();
$profile_username = isset($_GET["user_name"]) ? $_GET["user_name"] : $_SESSION["username"];
require("PDO.php");


// if(isset($_GET["source"]) && $_GET["source"] == "search"){
// $stmt = $pdo->prepare("SELECT `name`, user_name, pwd, email, profileContent  FROM users WHERE user_name = :user");
// $stmt->bindParam(":user", $_GET["user_name"]);

// $stmt->execute();
// }

if (isset($_GET["user_name"])) {
    $stmt = $pdo->prepare("SELECT id, `first_name`, `last_name`, user_name,   profileContent  FROM users WHERE user_name = :user"); //När inte GET source eller SESSION skickar något
    $stmt->bindParam(":user", $_GET["user_name"]);

    $stmt->execute();
} else {


    $stmt = $pdo->prepare("SELECT id, `first_name`, `last_name`, user_name,   profileContent  FROM users WHERE user_name = :user");
    $stmt->bindParam(":user", $_SESSION["username"]);

    $stmt->execute();
}


$result = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['profile_id'] = $result['id'];
$_SESSION['follow_username'] = $result['user_name'];
//     echo "<br>";
// var_dump($_SESSION);
//     echo "<br>";
// echo "<br>";

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="2"> -->
    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>
    <ul>

        <div class="profile-sidebar">
            <div class="profile-picture">
                <img src="./files/no_picture.jpg" alt="Profile picture">

            </div>
            <div class="edit-profile">
            <?php 
            
            $stmt = $pdo->prepare("SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id");
            $stmt->bindParam(":user_id", $_SESSION['id']);
            $stmt->bindParam(':follow_id', $_SESSION['profile_id']);
            $stmt->execute();
            $follow_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($_SESSION["id"]) && strcasecmp($_SESSION["username"], $profile_username) === 0) { ?>                
                <button><a href="edituser.php">Edit profile</a></button>
                <?php }
                else if (!$follow_result){ ?>
                     <form action="follow_user.php" method="GET" name="follow" style="display: inline;">
                     <button type="submit" name="id" value="<?php echo $result['id']; ?>">Follow</button>
                <?php } else if ($follow_result) { ?>
                    <form action="follow_user.php" method="GET" name="follow" style="display: inline;">
                    <button type="submit" name="id" value="<?php echo $result['id']; ?>">Unfollow</button>
                <?php } ?> 
                
            </div>
            <div class="profile-info">


                <ul>
                    <li>
                        <strong class="info-label"></strong> 
                        <span class="profile-content"><?php echo htmlspecialchars($result['first_name']) . " " . htmlspecialchars($result['last_name']); ?></span>
                    </li>

                    <li>
                    <strong class="info-label"></strong>
                     <span class="profile-content"><?php echo htmlspecialchars($result['user_name']); ?></li></span>
                    <li>
                        <strong class="info-label"></strong>
                        <span
                            class="profile-content"><?php echo htmlspecialchars($result['profileContent'] ?? 'No profile content available'); ?></span>
                    </li>
                </ul>


            </div>
        </div>
        <main class="profile-main">
            <div class="posts">               
                 <?php
                 
                 $sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, bp.CreatedDate, bp.image_base64, bp.user_id
                 FROM blogposts bp
                 JOIN users u ON bp.user_id = u.id
                 ORDER BY bp.CreatedDate DESC";
                 $stmt = $pdo->prepare($sql);
                 $stmt->execute();
                 $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                 foreach ($posts as $post): ?>
                   <?php if ($post['user_id'] != $_SESSION['profile_id']) {
                        continue;
                    } ?>
                    <div class="post">
                        <p class="post-username">
                            <ion-icon name="person-circle"></ion-icon><?php echo htmlspecialchars(ucwords(strtolower($post['user_name']))); ?>
                        </p>
                        <h3 class="post-title"><?php echo nl2br(htmlspecialchars($post['title'])); ?></h3>
                        <img src="<?php echo $post['image_base64'] ? 'data:image/png;base64,' . $post['image_base64'] : ''; ?>" alt="" class="post-img">
                        <p class="content short">
                            <?php echo nl2br(htmlspecialchars($post['blogContent'])); ?>
                        </p>
                        <button class="toggle-btn">Visa mer</button>
                        <!-- Comment Section -->
                        <div class="comments-section">
                            <h4>comment</h4>
                            <?php
                            
                            $commentSql = "SELECT c.commentContent, c.CreatedDate , u.user_name
                                       FROM comments c
                                       JOIN users u ON c.user_id = u.id
                                       WHERE c.blog_id = :blog_id
                                       ORDER BY c.CreatedDate DESC";
                            $commentStmt = $pdo->prepare($commentSql);
                            $commentStmt->bindParam(':blog_id', $post['id'], PDO::PARAM_INT);
                            $commentStmt->execute();
                            $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
                            $comments = array_reverse($comments);
    
                            foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <span id="user">
                                        <ion-icon name="person-circle"></ion-icon><strong><?php echo htmlspecialchars(ucwords(strtolower($comment['user_name'])))?> </strong> <?php echo "&nbsp;"  . htmlspecialchars($comment["CreatedDate"]); ?>
                                    </span>
                                    <?php echo htmlspecialchars($comment['commentContent']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
    
                        <form action="" method="post">
                            <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>" >
                            <input type="text" name="comment_input" placeholder="comment">
                            <button class="comment-btn" type="submit">Comment</button>
                        </form>
    
                        <button class="update-btn">Edit post</button>

                        
    
                        <?php if ($_SESSION['role'] == 1 || $post['user_id'] == $_SESSION['id']): ?>
                            <!-- Only allow the user who created the post or admins to delete -->
                            <form action="delete_post.php" method="POST" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="delete-btn">Delete post</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        </main>


    </ul>
    </div>
</body>

</html>