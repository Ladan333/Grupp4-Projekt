<?php
require("PDO.php");
session_start();

if(isset($_GET["source"]) && $_GET["source"] == "search"){
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :user");
$stmt->bindParam(":user", $_GET[""]);
}
else{
    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE user_name = :user");
    $stmt->bindParam(":user", $_SESSION["username"]);
    
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul>

    <?php foreach ($result as $key): ?>
    <li>
        <?php echo $result?>
    </li>
        <?php endforeach?>
        <?php var_dump($result)?>

    </ul>
    
</body>
</html>