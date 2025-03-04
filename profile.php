<?php
require("PDO.php");
session_start();


// if(isset($_GET["source"]) && $_GET["source"] == "search"){
// $stmt = $pdo->prepare("SELECT `name`, user_name, pwd, email, profileContent  FROM users WHERE user_name = :user");
// $stmt->bindParam(":user", $_GET["user_name"]);

// $stmt->execute();
// }

if(isset($_GET["user_name"])){
    $stmt = $pdo->prepare("SELECT `first_name`, 'last_name', user_name, pwd, email, profileContent  FROM users WHERE user_name = :user"); //När inte GET source eller SESSION skickar något
    $stmt->bindParam(":user", $_GET["user_name"]);
    
    $stmt->execute();
    }else{


    $stmt = $pdo->prepare("SELECT `first_name`, 'last_name', user_name, pwd, email, profileContent  FROM users WHERE user_name = :user");
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
    <title>Document</title>
</head>
<body>
    <ul>

   
    <?php foreach ($result as $key => $value): ?>
    <li>

        <?php echo "$key:  $value"; ?>

    </li>
    <?php endforeach?>
      
       

    </ul>
    
</body>
</html>