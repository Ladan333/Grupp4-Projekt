<?php
session_start();
require "PDO.php";

$result = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchUser = $_GET['search'];

    $stmt = $pdo->prepare("SELECT `name`, user_name FROM users WHERE user_name LIKE :searchUser ");
    $searchUser = "%" . $searchUser . "%";
    $stmt->bindParam(":searchUser", $searchUser);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


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

        <?php if (!empty($result)): ?>
            <ul>
                <?php foreach ($result as $row): ?>

                    <li class="searchResult">
                        <img src="./files/no_picture.jpg" alt="Profile picture" width="50" height="50">
                        <a href="profil.php?user_name=<?php echo urlencode($row['user_name']); ?>" class="profile-link">
                            <span class="name"><?php echo htmlspecialchars($row["name"]); ?></span>
                            <span class="username-search"><?php echo htmlspecialchars($row["user_name"]); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>
        <?php endif; ?>

    </main>
</body>

</html>