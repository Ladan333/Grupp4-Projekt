<?php
session_start();
require 'PDO.php'; 

if ($_SESSION['id'] == null) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Username';
$isAdmin = $_SESSION["role"] ?? false;



$sql = "SELECT bp.id, bp.title, bp.blogContent, u.user_name, bp.CreatedDate, bp.image_base64, bp.user_id
        FROM blogposts bp
        JOIN users u ON bp.user_id = u.id
        ORDER BY bp.CreatedDate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
  
                 


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home Page</title>
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
                    <textarea id="postContent" name="content" rows="4" required placeholder="Skriv ditt inlägg här..."></textarea>

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


        <div class="posts">
            <?php foreach ($posts as $post): ?>
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
                        
                        $commentSql = "SELECT c.commentContent, c.CreatedDate, u.user_name
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
                                    <ion-icon name="person-circle"></ion-icon><strong><?php echo htmlspecialchars(ucwords(strtolower($comment['user_name']))); ?>:</strong>
                                </span>
                                <?php echo htmlspecialchars($comment['commentContent']); ?>
                                <p><?php echo htmlspecialchars($comment['CreatedDate']) ?></p>
                            </div>
                            
                            

                        <?php endforeach; ?>
                    </div>

                    <form action="AddComments.php" method="POST">
                        <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>" >
                        <input type="text" name="comment_input" placeholder="comment">
                        <button class="comment-btn" type="submit">Comment</button>
                    </form>

                    <button class="update-btn">Edit post</button>

                    <?php if ($isAdmin || $post['user_id'] == $_SESSION['id']): ?>
                        <!-- Only allow the user who created the post or admins to delete -->
                        <form action="delete_post.php" method="POST" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="delete-btn">Delete post</button>
                        </form>
                        <?php var_dump($_POST); ?>
                    <?php endif; ?>
                    

                </div>
            <?php endforeach; ?>

                 
        </div>
                            
    </div>


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

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
                        this.textContent = "Visa mindre";
                    } else {
                        content.classList.add("short");
                        this.textContent = "Visa mer";
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

            openModalBtn.addEventListener("click", () => {
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
        });
    </script>

</body>

</html>
ta bort/ redigera användare