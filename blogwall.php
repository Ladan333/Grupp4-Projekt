<?php
session_start();
$username = $_SESSION['username'] ?? 'Username';
$isAdmin = $_SESSION["role"] ?? false;
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
                    <label for="postContent">Post text:</label>
                    <textarea id="postContent" name="content" rows="4" required placeholder="Skriv ditt inlägg här..."></textarea>

                    <label id="post-text">Upload image:</label>
                    <label for="postImage" class="postImage">
                        <ion-icon name="cloud-upload-sharp"></ion-icon>
                        <p id="image-names">Upload image</p>
                    </label>
                    <input type="file" id="postImage" name="image" accept="image/*">

                    <!-- Hidden input to store Base64-encoded image -->
                    <input type="hidden" name="image_base64" id="imageBase64">

                    <button type="submit" class="submit-btn">Publish</button>
                </form>
            </div>
        </div>


        <div class="posts">
            <div class="post">
                <p class="post-username"><ion-icon name="person-circle"></ion-icon><?php echo htmlspecialchars($username); ?></p>
                <img src="https://th.bing.com/th/id/OIP.shzeL1wiYB7Xigzm-vH0AgHaDt?rs=1&pid=ImgDetMain" alt="Blog image" class="post-img">
                <p class="content short">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                </p>
                <button class="toggle-btn">Visa mer</button>
                <!-- Comment Section -->
                <div class="comments-section">
                    <h4>Comments:</h4>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong> Alice:</strong>
                        </span>
                        Wow, detta var en intressant läsning!
                    </div>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong>Bob:</strong>
                        </span>
                        Håller med, jag gillade verkligen detta inlägg.
                    </div>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong>Christer:</strong>
                        </span>
                        Bra jobbat! Ser fram emot fler inlägg.
                    </div>
                </div>

                <button class="comment-btn">Comment</button>
                <button class="update-btn">Edit post</button>

                <?php if ($isAdmin): ?>
                    <button class="delete-btn">Delete post</button>
                <?php endif; ?>
            </div>

            <div class="post">
                <p class="post-username"><ion-icon name="person-circle"></ion-icon><?php echo htmlspecialchars($username); ?></p>
                <img src="https://th.bing.com/th/id/OIP.shzeL1wiYB7Xigzm-vH0AgHaDt?rs=1&pid=ImgDetMain" alt="Blog image" class="post-img">
                <p class="content short">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Non, eius aspernatur!
                    Sequi fuga doloremque soluta dolorem, expedita velit officia.
                </p>
                <button class="toggle-btn">Visa mer</button>

                <!-- Comment Section -->
                <div class="comments-section">
                    <h4>Comments:</h4>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong>David:</strong>
                        </span>
                        Mycket bra skrivet, ser fram emot nästa inlägg!
                    </div>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong>Eve:</strong>
                        </span>
                        Jag älskar hur du skriver!
                    </div>
                    <div class="comment">
                        <span id="user">
                            <ion-icon name="person-circle"></ion-icon><strong>Frank:</strong>
                        </span>
                        Håller med! Väldigt inspirerande.
                    </div>
                </div>

                <button class="comment-btn">Comment</button>
                <button class="update-btn">Edit post</button>

                <?php if ($isAdmin): ?>
                    <button class="delete-btn">Delete post</button>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".post").forEach(post => {
                let content = post.querySelector(".content");
                let button = post.querySelector(".toggle-btn");

                // Check if content is longer than 4 lines
                let isOverflowing = content.scrollHeight > content.clientHeight;

                if (!isOverflowing) {
                    button.style.display = "none"; // Hide the button if content is short
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
            });
            document.getElementById("postImage").addEventListener("change", function(event) {
                const file = event.target.files[0];
                const imageNames = document.getElementById("image-names");

                if (file) {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);

                    reader.onload = function() {
                        document.getElementById("imageBase64").value = reader.result;
                        imageNames.textContent = file.name;
                    };
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