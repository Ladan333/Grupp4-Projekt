<?php
// require 'user.php';
require_once "../Dao/UserDAO.php";
require_once "../övrigt/PDO.php";

require_once '../övrigt/display_count.php';
require_once '../Entity/userEntity.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_log("User ID: " . $user_id);
    error_log("Fetchcount: " . print_r($fetchcount, true));
}

$user = $_SESSION['user'];
$userrole = $user->getrole();

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" type="text/css" href="../css/CSS.css">
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
                <!-- messages and display count of unread messages -->
        <div class="display-messages">
            <a href="messages.php">&#9993;</a>

            <p id="unread-count" style="font-weight: bold; color: rgb(66, 135, 245);">
                <?= (isset($_SESSION['display_count']) && $_SESSION['display_count'] > 0) ? $_SESSION['display_count'] : '' ?>
            </p>
        </div>
        <!-- sub menu -->
        <div class="burger" onclick="toggleMenu()">
            <p>☰</p>
        </div>

        <ul class="submenu">
            <?php if (isset($_SESSION['user'])): ?>
                <?php


                ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="blogwall.php">Wall</a></li>
                <li><a href="../övrigt/logout.php">Logout</a></li>
                <li><a href="messages.php">Messages</a></li>

                <?php if ($userrole == 1): ?>
                    <li><a href="admin_list.php">Admin</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>


    </nav>
    <script>
        // Toggle function for the burger
        function toggleMenu() {
            let menu = document.querySelector(".submenu");


            if (menu) {
                menu.classList.toggle("active");
                console.log("Menu active state: " + menu.classList.contains("active"));
            } else {
                console.log("Menu not found!");
            }
        }
        // function to reload fetchcount for display every third second 
        function fetchUnreadCount() {
            fetch('../övrigt/fetch_unread.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched unread count:", data.unread_count); // ✅ Kontrollutskrift

                    let count = data.unread_count;
                    let unreadElement = document.getElementById("unread-count");

                    if (unreadElement) {
                        unreadElement.textContent = count > 0 ? count : '';
                    } else {
                        console.error("Elementet #unread-count hittades inte i DOM!");
                    }
                })
                .catch(error => console.error("Error fetching unread count:", error));
        }

        document.addEventListener('DOMContentLoaded', fetchUnreadCount);
        setInterval(fetchUnreadCount, 3000);
    </script>
</body>

</html>