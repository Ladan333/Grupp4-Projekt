<?php
require"PDO.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $name = $_POST["name"];
    
    $stmt = $pdo->prepare("SELECT user_name FROM users WHERE user_name = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt ->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt ->fetch(PDO::FETCH_ASSOC);

    if (!$result){
        $stmt = $pdo->prepare("INSERT INTO users (user_name, pwd, name, email) VALUES (:username, :password, :name, :email)");
        $stmt->bindParam(":username", $username);
        $hashwed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam("password", $hashwed_password);
        $stmt->bindParam("email", $email);
        $stmmt->bindParam("name", $name);

        header("Location: idnex.php");
        exit();
    } else{
        echo "User details already in use";
    };
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
        <label for="username"></label>
        <input name="username" id="username" type="text" required><br><br>
        <label for="password"></label>
        <input name="password" id="password" type="text" required><br><br>
        <label for="name"></label>
        <input name="name" id="name" type="text" required><br><br>
        <label for="email"></label>
        <input name="email" id="email" type="text" required><br><br>

        <input type="submit" value="Register">

    
    </form>

    
</body>
</html>