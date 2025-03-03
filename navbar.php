<?php


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta http-equiv="refresh" content="1">
    <title></title>
</head>
<body>
    <nav>
       <ul>
        <h2>The Wall</h2>
       </ul>
        <ul>
            <a href="logout.php">Profile</a>
            <a href="blogwall.php"> Wall</a>
            <?php
            if (isset($_SESSION['username'])) {
                echo '<a href="logout.php">Logout</a>';
            } else {
                echo '<a href="login.php">Login</a>';
            }?>
        </ul>


    </nav>    


</body>
</html>