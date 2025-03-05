<?php
session_start();
$profile_username = isset($_GET["user_name"]) ? $_GET["user_name"] : $_SESSION["username"];
require("PDO.php");


// if(isset($_GET["source"]) && $_GET["source"] == "search"){
// $stmt = $pdo->prepare("SELECT `name`, user_name, pwd, email, profileContent  FROM users WHERE user_name = :user");
// $stmt->bindParam(":user", $_GET["user_name"]);

// $stmt->execute();
// }

if (isset($_GET["user_name"])) {
    $stmt = $pdo->prepare("SELECT `first_name`, `last_name`, user_name,   profileContent  FROM users WHERE user_name = :user"); //När inte GET source eller SESSION skickar något
    $stmt->bindParam(":user", $_GET["user_name"]);

    $stmt->execute();
} else {


    $stmt = $pdo->prepare("SELECT `first_name`, `last_name`, user_name,   profileContent  FROM users WHERE user_name = :user");
    $stmt->bindParam(":user", $_SESSION["username"]);

    $stmt->execute();
}


$result = $stmt->fetch(PDO::FETCH_ASSOC);
//     echo "<br>";
// var_dump($_SESSION);
//     echo "<br>";
// echo "<br>";

// var_dump($result);

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="2"> -->
    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>
    <ul>

        <div class="profile-sidebar">
            <div class="profile-picture">
                <img src="./files/no_picture.jpg" alt="Profile picture">

            </div>
            <div class="edit-profile">
           <?php if (isset($_SESSION["username"]) && $_SESSION["username"] === $profile_username) { ?>
                <button><a href="edituser.php">Edit profile</a></button>
                <?php } ?>
            </div>
            <div class="profile-info">


                <ul>
                    <li>
                        <strong class="info-label"></strong> 
                        <span class="profile-content"><?php echo htmlspecialchars($result['first_name']) . " " . htmlspecialchars($result['last_name']); ?></span>
                    </li>

                    <li>
                    <strong class="info-label"></strong>
                     <span class="profile-content"><?php echo htmlspecialchars($result['user_name']); ?></li></span>
                    <li>
                        <strong class="info-label"></strong>
                        <span
                            class="profile-content"><?php echo htmlspecialchars($result['profileContent'] ?? 'No profile content available'); ?></span>
                    </li>
                </ul>


            </div>
        </div>
        <main clas="profile-main">
            <div>
                <!-- ADD POST FLOW FOR USER HERE -->
            </div>
        </main>


    </ul>
    </div>
</body>

</html>