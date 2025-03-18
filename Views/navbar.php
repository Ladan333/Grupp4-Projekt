<?php 
    // require 'user.php';
    require "../Dao/UserDAO.php";
    require "../övrigt/PDO.php";
   
    require_once('../övrigt/display_count.php');
    require_once '../Entity/userEntity.php';

    
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_log("User ID: " . $user_id);
error_log("Fetchcount: " . print_r($fetchcount, true));
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" type="text/css" href="CSS.css">
    <meta name="viewport" content="width=420, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- <meta http-equiv="refresh" content="1"> -->
    <title></title>
</head>

<body>
    <nav>
        <ul class="the-wall">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="blogwall.php" class="wall-link">
                    <h2>The Wall</h2>
                </a>


            <?php else: ?>
                <a href="index.php" class="wall-link">
                    <h2>The Wall</h2>
                </a>


            <?php endif; ?>
        </ul>
        <ul>
            <form action="search.php" method="GET" name="search">
                <input class="searchbar" type="text" placeholder="Search for user or post content" name="search">
             
            </form>
        </ul>

        <div class="display-messages">
            <a href="messages.php">&#9993;</a>
            <?php
            if ($_SESSION['display_count'] > 0) {?>
              <p id="unread-count" style="font-weight: bold; color: rgb(66, 135, 245);"></p>

            <?php } ?>
        </div>
        <div class="burger" onclick="toggleMenu()">
            <p>☰</p>
        </div>

        <ul class="submenu">
    <?php if (isset($_SESSION['user'])): ?>
        <?php 
        
      
        ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="blogwall.php">Wall</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="messages.php">Messages</a></li>
        
        <?php if (isset($_SESSION['role']) == 1): ?>
            <li><a href="admin_list.php">Admin</a></li>
        <?php endif; ?>
    <?php endif; ?>
</ul>


    </nav>
    <script>
        function toggleMenu() {
            let menu = document.querySelector(".submenu");


            if (menu) {
                menu.classList.toggle("active");
                console.log("Menu active state: " + menu.classList.contains("active"));
            } else {
                console.log("Menu not found!");
            }
        }

        function fetchUnreadCount() {
            fetch('fetch_unread.php')
                .then(response => response.json())
                .then(data => {
                    let count = data.unread_count;
                    let unreadElement = document.getElementById("unread-count");

                    if (unreadElement) {
                        unreadElement.textContent = count > 0 ? count : '';
                    }
                })
                .catch(error => console.error("Error fetching unread count:", error));
        }

        
        setInterval(fetchUnreadCount, 1000);

        
        window.onload = fetchUnreadCount;
    </script>
</body>

</html>