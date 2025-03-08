<?php
session_start();
require "PDO.php";

$result = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchUser = $_GET['search'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name LIKE :searchUser ");
    $searchUser = "%" . $searchUser . "%";
    $stmt->bindParam(":searchUser", $searchUser);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Fetch profile image from the database


// Set profile image (Base64 or default)




?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="main_search_result">

        <?php if (!empty($result)) { ?>
            <ul>
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
        <?php } else { ?>
            <p class="felmeddelande">Försök igen! Du sökte inte efter en existerande användare.</p>
            <p class="felmeddelande">Sök efter Namn eller Användarnamn</p>
        <?php } ?>


    </main>
</body>

</html>