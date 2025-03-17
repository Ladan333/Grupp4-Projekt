<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
require_once("PostsDAO.php");
require_once("UserDAO.php");
require "PDO.php";
if ($_SESSION['user'] == null) {
    header("Location: index.php");
    exit();
}
//fungerar nu så att om du söker posts men inte hittar det du söker och söker igen så kommer den fortsätta söka på posts. 
// men lämnar den sidan så defaultar den till sökning av användare. 
if (!isset($_SESSION['search_sort'])) {
    $_SESSION['search_sort'] = 1;
}



if (isset($_SESSION['last_page']) && $_SESSION['last_page'] !== 'search.php' && $_SERVER['PHP_SELF'] === '/search.php') {
    $_SESSION['search_sort'] = 1;
}

$_SESSION['last_page'] = basename($_SERVER['PHP_SELF']); 

$result = [];

if (isset($_GET['search']) && !empty($_GET['search']) && $_SESSION['search_sort'] == 1) {
    $searchUser = $_GET['search'];
    $_SESSION['search'] = $searchUser;

    $userDAO = new UserDAO($pdo);
    $result = $userDAO->searchUsersByLikeNameOrEmail($searchUser);

} else if (isset($_GET["search"]) && !empty($_GET["search"]) && $_SESSION["search_sort"] == 2) {
    $searchPost = $_GET["search"];
    $_SESSION["search"] = $searchPost;

    $postsDAO = new PostsDAO($pdo);
    $posts = $postsDAO->searchPosts($searchPost);
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
    <link rel="stylesheet" href="CSS.css">

    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>

    <form action="change_search_results.php" method="POST">
            <input type="hidden" name="change_view" value="1" ;>
            <button class="comment-btn" type="submit"><u>Switch between users and blog content!</u></button>
    </form>

   

        <?php if (!empty($result) && $_SESSION['search_sort'] == 1) { ?>
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

                        $postsDAO = new PostsDAO($pdo);
                        $comments = $postsDAO->getComments($post['id']);

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

        <div id="overlay"></div>
   
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
                        imageNamesLabel.textContent = "Current Image: " + image.getAttribute("src");
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
        document.addEventListener("DOMContentLoaded", function() {
            const driver = window.driver.js.driver;

            const driverObj = driver({
                showProgress: true,
                steps: [
                    {
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
                        element: ".blogflow",
                        popover: {
                            title: "Change Blogflow",
                            description: "Click here to filter posts based on the people you are following.",
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