<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'PDO.php';

if ($_SESSION['id'] == null) {
    header("Location: index.php");
    exit();
}
// Sessioncookie för auto-utloggnuing efter en timme. 
if (isset($_SESSION['login_time'])) {
    $session_lifetime = 3600;

    if (time() - $_SESSION['login_time'] > $session_lifetime) {

        session_destroy();
        header("Location: index.php");
        exit();
    }
} else {

    header("Location: index.php");
    exit();
}




if (!isset($_SESSION['sorting'])) {
    $_SESSION['sorting'] = 1;
}

if (isset($_SESSION['last_page']) && $_SESSION['last_page'] !== 'blogwall.php' && $_SERVER['PHP_SELF'] === '/blogwall.php') {
    $_SESSION['sorting'] = 1;
}

$_SESSION['last_page'] = basename($_SERVER['PHP_SELF']);





// if (!$_SESSION['blogflow'] == null) {
//     $_SESSION['blogflow'] = 1;
// }

// if (!$_SESSION['sorting'] == null) {
//     $_SESSION['sorting'] = 1;
// }

$username = $_SESSION['username'] ?? 'Username';
$isAdmin = $_SESSION["role"] ?? false;

// blogflow 1 = all posts, blogflow 2 = followed users posts
if ($_SESSION['blogflow'] == 1 || $_SESSION['blogflow'] == null) {

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
} else if ($_SESSION["blogflow"] == 2) {
    $stmt = $pdo->prepare("SELECT follow_id FROM follows WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $_SESSION['id']);
    $stmt->execute();
    $followed_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $followed_users = [];
    foreach ($followed_results as $result) {
        array_push($followed_users, $result["follow_id"]);
    }

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

    // if post does not have a followed user ID it will be removed from the blogflow
    foreach ($posts as $post) {
        if (!in_array($post["user_id"], $followed_users)) {
            $key = array_search($post, $posts);
            unset($posts[$key]);
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css" />
    <title>Home Page</title>
    <style>
        /* Position the help icon at the bottom right */
        .help-icon {
            position: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            bottom: 20px;
            right: 20px;
            font-size: 30px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            color: white;
            background-color: rgba(0, 0, 0, 0.9);
            border-radius: 50%;
            padding: 5px;
            transition: 0.3s ease-in-out;
        }

        .help-icon::before {
            content: "Show Tour";
            position: absolute;
            top: 50%;
            right: 60px;
            /* Positioning tooltip to the left */
            transform: translateY(-50%);
            background-color: black;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }

        /* Show tooltip on hover */
        .help-icon:hover::before {
            opacity: 1;
            visibility: visible;
        }

        .help-icon:hover {
            background-color: rgba(0, 0, 0, 0.5);
            transition: 0.3s ease-in-out;

        }

        .help-icon ion-icon {
            transition: 0.3s ease-in-out;
        }

        .help-icon ion-icon:hover {
            color: rgb(39, 39, 39);
            transition: 0.3s ease-in-out;
        }
    </style>

</head>

<body>
    <?php require "navbar.php"; ?>

    <div class="container">
        <div class="welcome-box">
            <h2>Welcome to our fantastic blog</h2>
            <!-- Add Post Button -->
            <button id="openModalBtn" class="add-post-btn"><ion-icon name="add-circle"></ion-icon> Add Post</button>
        </div>



        <!-- Add Post Modal -->
        <div id="postModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Add a new post</h2>
                <form class="add-post-form" action="add_post.php" method="POST" enctype="multipart/form-data">
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
        <div class="sorting">

            <?php
        
            if ($_SESSION['blogflow'] == 1) { ?>
                <form action="change_blogflow.php" method="POST">
                    <input type="hidden" name="change_view" value="1" ;>
                    <button class="comment-btn blogflow" type="submit">Sort by followers</button>
                </form>
            <?php } else { ?>
                <form action="change_blogflow.php" method="POST">
                    <input type="hidden" name="change_view" value="2" ;>
                    <button class="comment-btn blogflow" type="submit">Sort by all posts</button>
                </form>
            <?php } ?>

            <form action="sort_blogwall.php" method="POST">
                <input type="hidden" name="sort_recent" value="1" ;>
                <button class="comment-btn blogflow" type="submit">Sort by recent posts</button>
            </form>

            <form action="sort_blogwall.php" method="POST">
                <input type="hidden" name="sort_comment_count" value="2" ;>
                <button class="comment-btn blogflow" type="submit">Sort by most comments</button>
            </form>

            <form action="sort_blogwall.php" method="POST">
                <input type="hidden" name="sort_activity" value="3" ;>
                <button class="comment-btn blogflow" type="submit">Sort by most recent activity</button>
            </form>
        </div>


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
            <?php if (empty($posts)) : ?>
                <p class="empty_feed">No posts could be found<br></p>
                <p class="empty_feed">Try following some users or add a post yourself!</p>
            <?php endif; ?>
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
                    <button class="toggle-btn">Show more</button>
                    
                     <!-- kod för gilla-knapp -->
                    <?php
                    // Hämta antalet gillningar för inlägget
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                    $stmt->execute([$post['id']]);
                    $like_count = $stmt->fetchColumn();
                    ?>
                    <button  class="like-btn" data-post-id="<?= $post['id']; ?>">
                    ❤️ <span class="like-count"><?= $like_count; ?></span>
                     </button>

                    
                    <!-- Comment Section -->
                    <div class="comments-section">
                        <h4>comment</h4>
                        <?php


                        $commentSql = "SELECT c.id, c.commentContent, c.CreatedDate, u.user_name, u.profile_image

                       

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
                                    <?php if ($isAdmin || $post['user_id'] == $_SESSION['id']): ?>
                                        <!-- Only allow the user who created the post or admins to delete -->
                                        <form action="delete_comment.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="delete_comment" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="delete-btn">X</button>
                                        </form>

                                    <?php endif; ?>
                                </div>
                                </div>
                                <p><?php echo htmlspecialchars($comment['commentContent']); ?></p>
                            </div>



                        <?php endforeach; ?>
                    </div>

                    <form action="AddComments.php" id="addComments-form" method="POST">

                        <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

                        <input class="comment-input" type="text" name="comment_input" placeholder="comment" required>

                        <button class="comment-btn" type="submit">Comment</button>
                    </form>
                    <?php if ($post['user_id'] == $_SESSION['id']): ?>
                        <button class="update-btn">Edit post</button>
                    <?php endif; ?>
                    <?php if ($isAdmin || $post['user_id'] == $_SESSION['id']): ?>
                        <!-- Only allow the user who created the post or admins to delete -->
                        <form action="delete.php" method="POST" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="delete-btn">Delete post</button>
                        </form>

                    <?php endif; ?>


                </div>
            <?php endforeach; ?>


        </div>

    </div>
    <div id="overlay"></div>
    <!-- Help Icon -->
    <div class="help-icon" id="start-tour">
        <ion-icon name="help-circle"></ion-icon>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".post").forEach(post => {
                let content = post.querySelector(".content");
                let button = post.querySelector(".toggle-btn");


                let isOverflowing = content.scrollHeight > content.clientHeight;

                if (!isOverflowing) {
                    button.style.display = "none";
                }

                button.addEventListener("click", function() {
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
            document.getElementById("postImage").addEventListener("change", function(event) {
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
                button.addEventListener("click", function() {
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
                        const username = "<?php echo htmlspecialchars(ucwords(strtolower($username))); ?>"; // Get the username of the person who posted
                        imageNamesLabel.textContent = `${username}'s post image`;
                    } else {
                        imageNamesLabel.textContent = "Upload Image";
                    }

                    // Change form action for editing
                    form.insertAdjacentHTML("beforeend", `<input type="hidden" name="post_id" value="${postId}">`);

                    // Update modal appearance
                    modalTitle.textContent = "Edit Post";
                    submitButton.textContent = "Update Post";
                    editMode = true;
                    editPostId = postId;
                    form.action = "edit_post.php";

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
        document.addEventListener("DOMContentLoaded", function() {
            const driver = window.driver.js.driver;

            const driverObj = driver({
                showProgress: true,
                steps: [{
                        element: ".container",
                        popover: {
                            title: "BlogWall page",
                            description: "Here you can see all the blogs that has been posted by different users.",
                            side: "left",
                            align: 'start'
                        }
                    },
                    {
                        element: ".add-post-btn",
                        popover: {
                            title: "Add Post",
                            description: "Add post modal, displays a modal so that the user can add posts.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: ".sorting",
                        popover: {
                            title: "Sorting Posts",
                            description: "Click here to filter posts based on what you would like.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: ".post",
                        popover: {
                            title: "Post",
                            description: "Posts added by users displayed here.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: "#addComments-form",
                        popover: {
                            title: "Commenting section",
                            description: "Here you can add comments to each post.",
                            side: "top",
                            align: 'start'
                        }
                    },
                    {
                        element: ".update-btn",
                        popover: {
                            title: "Edit Post",
                            description: "Click here to opent the Edit post modal in order to edit your posts.",
                            side: "top",
                            align: 'start'
                        }
                    },
                    {
                        element: ".delete-btn",
                        popover: {
                            title: "Delete Post",
                            description: "Click here to delete posts.",
                            side: "left",
                            align: 'start'
                        }
                    },

                ]
            });

            // Start the tour when the help icon is clicked
            document.getElementById("start-tour").addEventListener("click", function() {
                driverObj.drive();
            });
        });
    </script>

</body>

</html>
