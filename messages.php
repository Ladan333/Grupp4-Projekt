<?php
$cookie_name = "user_session";
$cookie_value = session_id();
$cookie_time = time() + 3600;

setcookie($cookie_name, $cookie_value, $cookie_time, "/", "", false, true);


session_start();
require_once 'PDO.php';




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="20"> -->
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <link preload href="./files/Leche_Frita.ttf" as="font" type="font/ttf" crossorigin>
    <title>Document</title>
</head>

<body>
<?php require "navbar.php"; ?>


    <main class="index">



    <div>
        <php? foreach(): ?>
            

            <php? endforeach ?>

    </div>

    <div>

    </div>


    <div class="comments-section">
                        
    <form action="AddComments.php" id="addComments-form" method="POST">

                        <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

                        <input class="comment-input" type="text" name="comment_input" placeholder="comment" required>

                        <button class="comment-btn" type="submit">Comment</button>
                    </form>
    <h4>comment</h4>

                        <?php


                        $commentSql = "SELECT c.commentContent, c.CreatedDate, u.user_name, u.profile_image

                       

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
                                    <?php $profile_img = !empty($comment['profile_image']) ? "data:image/png;base64," . htmlspecialchars($comment['profile_image']) : "./files/no_picture.jpg"; ?>
                                    <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="30" height="30"
                                        style="border-radius:50%;"><strong><a
                                            href="profile.php?user_name=<?= urlencode($comment['user_name']) ?>" class="profile-link">
                                            <?= "&nbsp;&nbsp;" . htmlspecialchars(ucwords(strtolower($comment['user_name']))) ?>

                                        </a></strong>
                                </span>
                                <?php echo htmlspecialchars($comment['commentContent']); ?>
                                <p><?php echo htmlspecialchars($comment['CreatedDate']) ?></p>
                            </div>



                        <?php endforeach; ?>
                    </div>
       
    </main>

</body>

</html>