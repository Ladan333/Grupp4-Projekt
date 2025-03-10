<?php
$cookie_name = "user_session";
$cookie_value = session_id(); 
$cookie_time = time() + 3600; 

setcookie($cookie_name, $cookie_value, $cookie_time, "/", "", false, true); 


session_start();
require "PDO.php";

$_SESSION['sorting'] = 1;
$_SESSION['blogflow'] = 1;
    if (isset($_SESSION["username"])) {
        header("Location: blogwall.php");
        exit();
    }
    
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $stmt = $pdo->prepare("SELECT id, pwd, role FROM users WHERE user_name = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result_userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $hashwed_password = $result_userinfo['pwd'];
        
       
        if (!$result_userinfo) {
            echo"invalid";
        }
        else if (password_verify($password, $hashwed_password)){
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $result_userinfo['role'];
            $_SESSION['id'] = $result_userinfo['id'];
            $_SESSION['login_time'] = time();
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
        <!-- <meta http-equiv="refresh" content="20"> -->
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <link preload href="./files/Leche_Frita.ttf" as="font" type="font/ttf" crossorigin>
    <title>Document</title>
</head>
<body>
    <?php require"navbar.php"; ?>
    
    <main class="index">
    
    <form class="login" action="index.php" method="POST">
        <h2>Welcome to <br>The Wall</h2>
        <label for="username">Username</label>
        <input class="login_Input" name="username" id="username" type="text" placeholder="Input username"required>
        <br>
        <label for="password">Password</label>
        <input class="login_Input" name="password" id="password" type="password" placeholder="Input password" required>

        <button class="button" type="submit" value="login">Login</button>
        <a class="forgotpass" href="forgotpassword.php">Forgot password?</a>
        <br>
        <p class="pindex">No account?</p><a href="register.php">Register here</a>
        
    
   
    </form>
    </main>
</body>
</html>