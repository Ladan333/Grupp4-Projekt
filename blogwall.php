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
            <h2>Välkommen till vår fantastiska blogg</h2>
        </div>

        <div class="posts">
            <div class="post">
                <p class="username"><ion-icon name="person-circle"></ion-icon><?php echo htmlspecialchars($username); ?></p>
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
                    <h4>Kommentarer:</h4>
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

                <button class="comment-btn">Kommentera</button>
                <button class="update-btn">Ändra inlägg</button>

                <?php if ($isAdmin): ?>
                    <button class="delete-btn">Ta bort inlägg</button>
                <?php endif; ?>
            </div>

            <div class="post">
                <p class="username"><ion-icon name="person-circle"></ion-icon><?php echo htmlspecialchars($username); ?></p>
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
                    <h4>Kommentarer:</h4>
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

                <button class="comment-btn">Kommentera</button>
                <button class="update-btn">Ändra inlägg</button>

                <?php if ($isAdmin): ?>
                    <button class="delete-btn">Ta bort inlägg</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

                
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".post").forEach(post => {
            let content = post.querySelector(".content");
            let button = post.querySelector(".toggle-btn");

            // Check if content is longer than 4 lines
            let isOverflowing = content.scrollHeight > content.clientHeight;

            if (!isOverflowing) {
                button.style.display = "none"; // Hide the button if content is short
            }

            button.addEventListener("click", function () {
                if (content.classList.contains("short")) {
                    content.classList.remove("short");
                    this.textContent = "Visa mindre";
                } else {
                    content.classList.add("short");
                    this.textContent = "Visa mer";
                }
            });
        });
    });
</script>

</body>

</html>