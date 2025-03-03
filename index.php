<?php
session_start();
require"PDO.php";





    if (isset($_SESSION["username"])) {
        header("Location: blogwall.php");
        exit();
    }
    
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $stmt = $pdo->prepare("SELECT pwd FROM users WHERE user_name = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result_userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $hashwed_password = $result_userinfo['pwd'];

        if (!$result_userinfo) {
            echo"invalid";
        }
        else if (password_verify($password, $hashwed_password)){
            $_SESSION['username'] = $username;
           
            header("Location: blogwall.php");
            exit();
        }
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


    
    <form action="index.php" method="POST">
        <label for="username">Username</label><br>
        <input name="username" id="username" type="text" required><br><br>
        <label for="password">Password</label><br>
        <input name="password" id="password" type="text" required><br><br>

        <input type="submit" value="login">

    
    </form>

    
</body>
</html>