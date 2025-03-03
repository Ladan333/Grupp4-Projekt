<?php
require "PDO.php"; 

$result = [];


if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchUser = $_GET['search'];

    $stmt = $pdo->prepare("SELECT `name`, user_name FROM users WHERE user_name LIKE :searchUser ");
    $searchUser = "%".$searchUser."%";
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
    <title>Search Results</title>
</head>
<body>
    <h2>Search Results</h2>

    <div>
        <?php if (!empty($result)): ?>
            <ul>
                <?php foreach ($result as $row): ?>
                    <li>
                        <a href="profil.php?user_name=<?php echo urlencode($row['user_name']); ?>" style="text-decoration: none">
                            <?php echo htmlspecialchars($row["name"]) . " (" . htmlspecialchars($row["user_name"]) . ")"; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
