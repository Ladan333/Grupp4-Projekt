<?php 
session_start(); 
require_once("PDO.php");
$id = $_SESSION['id']; // Current logged-in user

$query = "SELECT u.id, u.first_name, u.last_name, u.user_name
          FROM follows f
          JOIN users u ON f.follow_id = u.id
          WHERE f.user_id = :id
          ORDER BY u.first_name DESC";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);




?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS.css">
    <title>Document</title>
</head>
<body>
<?php require "navbar.php"?>     

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

</body>
</html>