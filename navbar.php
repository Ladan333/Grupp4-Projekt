<?php
if(session_status() == PHP_SESSION_NONE) {session_start();}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <!-- <meta http-equiv="refresh" content="1"> -->
    <title></title>
</head>
<body>
    <nav>
       <ul>
        <h2>The Wall</h2>
       </ul>
       <ul>
       <form action="search.php" method="GET" name="search">
    <input class="searchbar" type="text" placeholder="Search user" name="search">
    <button class="buttonsearch" type="submit">Search</button>
</form>
       </ul>
        <ul>
            <?php
            if (isset($_SESSION['username'])) {
                echo '<a href="profile.php">Profile</a>';
                echo '<a href="blogwall.php">Wall</a>';
                echo '<a href="logout.php">Logout</a>';
                if(isset($_SESSION['role']) == 1) {
                    echo '<a href="admin.php">Admin</a>';
                } 
            } else {
                echo '<a href="register.php">Register</a>';

            }?>
        </ul>


    </nav>    


</body>
</html>