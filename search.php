<?php
session_start();
require "PDO.php";

$result = [];

if (isset($_GET['search']) && !empty($_GET['search']) && $_SESSION['search_sort'] == 1) {
    $searchUser = $_GET['search'];
    $_SESSION['search'] = $searchUser;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name LIKE :searchUser ");
    $searchUser = "%" . $searchUser . "%";
    $stmt->bindParam(":searchUser", $searchUser);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else if (isset($_GET["search"]) && !empty($_GET["search"]) && $_SESSION["search_sort"] == 2) {
    $searchPost = $_GET["search"];
    $_SESSION["search"] = $searchPost;

    $stmt = $pdo->prepare("SELECT * FROM blogposts AS bp JOIN users AS u ON bp.user_id = u.id WHERE title LIKE :searchPost OR blogContent LIKE :searchPost");
    $searchPost = "%". $searchPost . "%";
    $stmt->bindParam("searchPost", $searchPost);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Fetch profile image from the database


// Set profile image (Base64 or default)




?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <!-- <link rel="stylesheet" href="CSS.css"> -->

    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>

    <form action="change_search_results.php" method="POST">
            <input type="hidden" name="change_view" value="1" ;>
            <button class="comment-btn" type="submit">Change search results</button>
    </form>

    <main class="main_search_result">

        <?php if (!empty($result) && $_SESSION['search_sort'] == 1) { ?>
            <ul>
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

            </ul>
        <?php } else if (!empty($posts) && $_SESSION['search_sort'] == 2) { ?>
            <div class="posts">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p class="post-username">
                        <?php $profile_img = !empty($post['profile_image']) ? "data:image/png;base64," . $post['profile_image'] : "./files/no_picture.jpg"; ?>

                        <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="50" height="50"
                            style="border-radius:50%;"> <a href="profile.php?user_name=<?= urlencode($post['user_name']) ?>"
                            class="profile-link">
                            <?= "&nbsp;&nbsp;" . htmlspecialchars(ucwords(strtolower($post['user_name']))) ?>
                        </a>
                    </p>
                    <div class="postDate">
                        <h3 class="post-title"><?php echo nl2br(htmlspecialchars($post['title'])); ?></h3>
                        <p><small>Posted on: <?php echo $post['CreatedDate']; ?></small></p>
                    </div>
                    <?php if (!empty($post['image_base64'])): ?>
                        <img src="data:image/png;base64,<?php echo $post['image_base64']; ?>" alt="" class="post-img">
                    <?php endif; ?>

                    <p class="content short">
                        <?php echo nl2br(htmlspecialchars($post['blogContent'])); ?>
                    </p>
                    <button class="toggle-btn">Visa mer</button>
                    <!-- Comment Section -->
                    <div class="comments-section">
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
                </div>
            <?php endforeach; ?>
        <?php } else { ?>
            <p class="felmeddelande">Försök igen! Du sökte inte efter en existerande användare.</p>
            <p class="felmeddelande">Sök efter Namn eller Användarnamn</p>
        <?php } ?>


    </main>
</body>

</html>