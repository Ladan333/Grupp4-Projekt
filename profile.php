<?php
session_start();
$profile_username = isset($_GET["user_name"]) ? $_GET["user_name"] : $_SESSION["username"];
require("PDO.php");

if ($_SESSION['id'] == null) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['sorting'])) {
    $_SESSION['sorting'] = 1;
}

if (isset($_SESSION['last_page']) && $_SESSION['last_page'] !== 'profile.php' && $_SERVER['PHP_SELF'] === '/profile.php') {
    $_SESSION['sorting'] = 1;
}

$_SESSION['last_page'] = basename($_SERVER['PHP_SELF']);

// if (!$_SESSION['blogflow'] == null) {
//     $_SESSION['blogflow'] = 1;
// }

// if (!$_SESSION['sorting'] == null) {
//     $_SESSION['sorting'] = 1;
// }


// if(isset($_GET["source"]) && $_GET["source"] == "search"){
// $stmt = $pdo->prepare("SELECT `name`, user_name, pwd, email, profileContent  FROM users WHERE user_name = :user");
// $stmt->bindParam(":user", $_GET["user_name"]);

// $stmt->execute();
// }

if (isset($_GET["user_name"])) {
    $stmt = $pdo->prepare("SELECT id, `first_name`, `last_name`, user_name, profile_image,  profileContent  FROM users WHERE user_name = :user"); //När inte GET source eller SESSION skickar något
    $stmt->bindParam(":user", $_GET["user_name"]);

    $stmt->execute();
} else {


    $stmt = $pdo->prepare("SELECT id, `first_name`, `last_name`, user_name, profile_image,  profileContent  FROM users WHERE user_name = :user");
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
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="CSS.css">

    <!-- <meta http-equiv="refresh" content="2"> -->
    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>
    <main class="profile-main">
        <section class="posts-section">
            <?php if (isset($_SESSION["id"]) && strcasecmp($_SESSION["username"], $profile_username) === 0) { ?>
                <div class="welcome-box">
                    <!-- <h2>W elcome to </h2> -->
                    <!-- Add Post Button -->
                    <button id="openModalBtn" class="add-post-btn"><ion-icon name="add-circle"></ion-icon> Add Post</button>
                </div>



                <div id="postModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h2>Add a new post</h2>

                        <form class="add-post-form" action="add_post.php" method="POST" enctype="multipart/form-data">

                            <input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

                            <label for="add-post-title">Title:</label>
                            <input type="text" id="add-post-title" name="title" required placeholder="Amazing blogwall...">

                            <label for="postContent">Post text:</label>
                            <textarea id="postContent" name="content" rows="4" required
                                placeholder="Skriv ditt inlägg här..."></textarea>

                            <label id="post-text">Upload image:</label>
                            <label for="postImage" class="postImage">
                                <ion-icon name="cloud-upload-sharp"></ion-icon>
                                <p id="image-names">Upload image</p>
                            </label>
                            <input type="file" id="postImage" name="image" accept="image/*">

                            <button type="submit" class="submit-btn">Publish</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
            <div class="sorting">
                <form action="sort_blogwall_profile.php" method="POST">
                    <input type="hidden" name="sort_recent" value="2" ;>
                    <button class="comment-btn blogflow" type="submit">Sort by recent posts</button>
                </form>

                <form action="sort_blogwall_profile.php" method="POST">
                    <input type="hidden" name="sort_comment_count" value="3" ;>
                    <button class="comment-btn blogflow" type="submit">Sort by most comments</button>
                </form>

                <form action="sort_blogwall_profile.php" method="POST">
                    <input type="hidden" name="sort_activity" value="4" ;>
                    <button class="comment-btn blogflow" type="submit">Sort by recent activity</button>
                </form>
            </div>

            <?php
                            if ($_SESSION['sorting'] == 1) {
                                $sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate,  bp.image_base64, bp.user_id
                             FROM blogposts bp
                             JOIN users u ON bp.user_id = u.id
                             ORDER BY bp.CreatedDate DESC";
                            } else if ($_SESSION['sorting'] == 2) {
                                $sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate,  bp.image_base64, bp.user_id, COUNT(c.blog_id)
                             FROM blogposts bp
                             JOIN users AS u ON bp.user_id = u.id JOIN comments AS c ON c.blog_id = bp.id
                             GROUP BY c.blog_id
                             ORDER BY COUNT(c.blog_id) DESC, bp.CreatedDate DESC";
                            } else if ($_SESSION["sorting"] == 3) {
                                $sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate,  bp.image_base64, bp.user_id, COUNT(c.blog_id)
                            FROM blogposts bp
                            JOIN users AS u ON bp.user_id = u.id JOIN comments AS c ON c.blog_id = bp.id
                            WHERE c.CreatedDate >= NOW() - INTERVAL 1 DAY
                            GROUP BY c.blog_id
                            ORDER BY COUNT(c.blog_id) DESC, bp.CreatedDate DESC";
                            }
            
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($posts) && $_SESSION['profile_id'] == $_SESSION['id']) {
                            for ($x = 0; $x <= count($posts); $x++) : 
                                if ($posts[$x]["user_id"] != $_SESSION["profile_id"]) {
                                    unset($posts[$x]);
                            }
                            endfor;
                            }
                               
            ?>

            <div class="posts">
            <style>
                .empty_feed {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100%;
                    font-size: 1.5rem;
                }
            </style>
            <?php if (empty($posts) && $_SESSION['profile_id'] == $_SESSION['id']) : ?>
                <p class="empty_feed">No posts could be found<br></p>
                <p class="empty_feed">Try adding some posts for others to see!</p>
            <?php endif; ?>

            <?php if (empty($posts) && $_SESSION['profile_id'] != $_SESSION['id']) : ?>
                <p class="empty_feed">No posts could be found<br></p>
                <p class="empty_feed">This user has made no posts matching the criteria!</p>
            <?php endif; ?>

                <?php

                foreach ($posts as $post): ?>
                    <?php if ($post['user_id'] != $_SESSION['profile_id']) {
                        continue;
                    } ?>
                    <div class="post">
                        <p class="post-username">
                            <!-- hämta ut bilderna innuti loopen på -->
                            <?php $profile_img = !empty($post['profile_image']) ? "data:image/png;base64," . $post['profile_image'] : "./files/no_picture.jpg"; ?>

                            <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="50" height="50"
                                style="border-radius:50%;"><strong><a
                                    href="profile.php?user_name=<?= urlencode($post['user_name']) ?>" class="profile-link">
                                    <?= "&nbsp;&nbsp;" . htmlspecialchars(ucwords(strtolower($post['user_name']))) ?>
                                </a></strong>
                        <h3 class="post-title"><?php echo nl2br(htmlspecialchars($post['title'])); ?></h3>
                        <?php if (!empty($post['image_base64'])): ?>
                            <img src="data:image/png;base64,<?php echo $post['image_base64']; ?>" alt="" class="post-img">
                        <?php endif; ?>
                        <p class="content short">
                            <?php echo nl2br(htmlspecialchars($post['blogContent'])); ?>
                        </p>
                        <button class="toggle-btn">Show more</button>
                        <!-- Comment Section -->
                        <div class="comments-section">
                            <h4>comment</h4>
                            <?php

                            $commentSql = "SELECT c.id, c.commentContent, c.CreatedDate , u.user_name, u.profile_image
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
                                <div class="comment-header">
                                <div id="user">
                                    <?php $profile_img = !empty($comment['profile_image']) ? "data:image/png;base64," . htmlspecialchars($comment['profile_image']) : "./files/no_picture.jpg"; ?>
                                    <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="30" height="30"
                                        style="border-radius:50%;"><strong><a
                                            href="profile.php?user_name=<?= urlencode($comment['user_name']) ?>" class="profile-link">
                                            <?= "&nbsp;&nbsp;" . htmlspecialchars(ucwords(strtolower($comment['user_name']))) ?>
                                            <?php echo "&nbsp;&nbsp;" . htmlspecialchars($comment['CreatedDate']) ?>

                                        </a></strong>
                                </div>
                                <div id="comment-delete-btn">

                                        <!-- Only allow the user who created the post or admins to delete -->
                                        <form action="delete_comment.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="delete_comment" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="delete-btn">X</button>
                                        </form>

                                </div>
                                </div>
                                <p><?php echo htmlspecialchars($comment['commentContent']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <form action="Addcomments.php" method="post">

                            <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">

                            <input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

                            <input class="comment-input" type="text" name="comment_input" placeholder="Comment" required>
                            <button class="comment-btn" type="submit">Comment</button>
                        </form>

                        <?php if ($post['user_id'] == $_SESSION['id']): ?>
                            <button class="update-btn">Edit post</button>
                        <?php endif; ?>



                        <?php if ($_SESSION['role'] == 1 || $post['user_id'] == $_SESSION['id']): ?>
                            <!-- Only allow the user who created the post or admins to delete -->
                            <form action="delete.php" method="POST" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="delete-btn">Delete post</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        </section>
        <section class="sidebar-section">
            <div class="profile-sidebar">
                <?php

                $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_name = :user");
                $stmt->bindParam(":user", $profile_username);
                $stmt->execute();
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);


                $profile_img = !empty($userData['profile_image']) ? "data:image/png;base64," . htmlspecialchars($userData['profile_image']) : "./files/no_picture.jpg";
                ?>
                <div class="profile-picture">
                    <img src="<?= $profile_img ?>" alt="Profile picture">
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
                        <button><a href="following.php">Follows</a></button>
                    <?php } else if (!$follow_result) { ?>
                            <form action="follow_user.php" method="GET" name="follow" style="display: inline;">
                                <button type="submit" value="<?php echo $result['id']; ?>">Follow</button>
                            </form>
                           
                            <button><a href="m2m.php?user_name=<?php echo urlencode($result['user_name']); ?>">Send Messages</a></button>
                            <?php } else if ($follow_result) { ?>
                                <form action="follow_user.php" method="GET" name="follow" style="display: inline;">
                                    <button type="submit" name="id" value="<?php echo $result['id']; ?>">Unfollow</button>
                                    <button><a href="m2m.php?user_name=<?php echo urlencode($result['user_name']); ?>">Send Messages</a></button>

                                    <?php } ?>

                                </form>

                </div>
                <div class="profile-info">


                    <ul>
                        <li>
                            <strong class="info-label"></strong>
                            <span
                                class="profile-content"><?php echo htmlspecialchars($result['first_name']) . " " . htmlspecialchars($result['last_name']); ?></span>
                        </li>

                        <li>
                            <strong class="info-label"></strong>
                            <span class="profile-content"><?php echo htmlspecialchars($result['user_name']); ?>
                        </li></span>
                        <li>
                            <strong class="info-label"></strong>
                            <span
                                class="profile-content"><?php echo htmlspecialchars($result['profileContent'] ?? 'No profile content available'); ?></span>
                        </li>
                    </ul>


                </div>
            </div>
        </section>

    </main>



    <div id="overlay"></div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".post").forEach(post => {
                let content = post.querySelector(".content");
                let button = post.querySelector(".toggle-btn");


                let isOverflowing = content.scrollHeight > content.clientHeight;

                if (!isOverflowing) {
                    button.style.display = "none";
                }

                button.addEventListener("click", function () {
                    if (content.classList.contains("short")) {
                        content.classList.remove("short");
                        this.textContent = "Show less";
                    } else {
                        content.classList.add("short");
                        this.textContent = "Show more";
                    }
                });

                // const deleteBtn = post.querySelector(".delete-btn");
                // if (deleteBtn) {
                //     deleteBtn.addEventListener("click", function(event) {

                //         event.preventDefault();


                //         const confirmed = confirm("Are you sure you want to delete this post?");


                //         if (confirmed) {

                //             const form = post.querySelector("form");
                //             if (form) {
                //                 form.submit(); 
                //             }
                //         }
                //     });
                // }
            });
            document.getElementById("postImage").addEventListener("change", function (event) {
                const fileInput = event.target;
                const fileNameDisplay = document.getElementById("image-names");

                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name;
                } else {
                    fileNameDisplay.textContent = "Upload Image";
                }
            });
            const modal = document.getElementById("postModal");
            const openModalBtn = document.getElementById("openModalBtn");
            const closeBtn = document.querySelector(".close-btn");
            const modalTitle = modal.querySelector("h2");
            const submitButton = modal.querySelector(".submit-btn");
            const postTitleInput = document.getElementById("add-post-title");
            const postContentInput = document.getElementById("postContent");
            const postImageInput = document.getElementById("postImage");
            const imageNamesLabel = document.getElementById("image-names");
            const form = modal.querySelector(".add-post-form");
            let editMode = false; // Track if we're editing a post
            let editPostId = null; // Store the post ID being edited

            openModalBtn.addEventListener("click", () => {
                resetModal();
                modal.style.display = "flex";
            });

            closeBtn.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
            document.querySelectorAll(".update-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const post = this.closest(".post"); // Get the parent post element
                    const postId = post.querySelector("input[name='post_id']")?.value; // Get post ID
                    const title = post.querySelector(".post-title").textContent.trim();
                    const content = post.querySelector(".content").textContent.trim();
                    const image = post.querySelector(".post-img");

                    // Fill the form with existing post data
                    postTitleInput.value = title;
                    postContentInput.value = content;

                    // Handle image display
                    if (image) {
                        const username = "<?php echo htmlspecialchars(ucwords(strtolower($profile_username))); ?>"; // Get the username of the person who posted
                        imageNamesLabel.textContent = `${username}'s post image`;
                    } else {
                        imageNamesLabel.textContent = "Upload Image";
                    }

                    // Change form action for editing
                    form.action = "edit_post.php";
                    form.insertAdjacentHTML("beforeend", `<input type="hidden" name="post_id" value="${postId}">`);

                    // Update modal appearance
                    modalTitle.textContent = "Edit Post";
                    submitButton.textContent = "Update Post";
                    editMode = true;
                    editPostId = postId;

                    // Open modal
                    modal.style.display = "flex";
                });
            });
            // Reset modal fields
            function resetModal() {
                postTitleInput.value = "";
                postContentInput.value = "";
                postImageInput.value = "";
                imageNamesLabel.textContent = "Upload Image";

                modalTitle.textContent = "Add a New Post";
                submitButton.textContent = "Publish";

                if (editMode) {
                    document.querySelector("input[name='post_id']")?.remove();
                    editMode = false;
                    editPostId = null;
                }

                form.action = "add_post.php";
            }
            const images = document.querySelectorAll(".post-img");
            const overlay = document.getElementById("overlay");
            images.forEach(img => {
                img.addEventListener("mouseenter", () => {
                    overlay.style.visibility = "visible"; // Show the overlay
                    overlay.style.opacity = "1"; // Make it visible
                });

                img.addEventListener("mouseleave", () => {
                    overlay.style.visibility = "hidden"; // Hide the overlay
                    overlay.style.opacity = "0"; // Fade it out
                });
            });
        });
    </script>

</body>

</html>