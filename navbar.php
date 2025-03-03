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
            <?php
            if (isset($_SESSION['username'])) {
                echo '<a href="profile.php">Profile</a>';
                echo '<a href="blogwall.php">Wall</a>';
                echo '<a href="logout.php">Logout</a>';

            } else {
                echo '<a href="register.php">Register</a>';

            }?>
        </ul>


    </nav>    


</body>
</html>