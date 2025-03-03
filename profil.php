<?php
require("PDO.php");

if(isset($_GET["source"]) && $_GET["source"] == "search"){
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :user");
$stmt->bindParam(":user", $_GET[""]);
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
    <?php 
    echo $_GET["user_name"];
    ?>
    
</body>
</html>