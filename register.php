<?php
session_start();
session_destroy();
session_start();

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
        $stmt->bindParam("name", $name);
        if ($stmt->execute()){
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt ->execute();
            $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userInfo){
                $_SESSION['username'] = $userInfo['user_name'];
                $_SESSION['name'] = $userInfo['name'];
                $_SESSION['email'] = $userInfo['email'];
                $_SESSION['role'] = $userInfo['role'];

                header("Location: blogwall.php");
                exit();
            }
        }
        else {
            header("Location: register.php");
            exit();
        }
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
        <meta http-equiv="refresh" content="5">
    <title>Document</title>
</head>
<body>
<?php require"navbar.php"; ?>

<main class="index">
    <form class="login" action="register.php" method="POST">
        <label class="label_register" for="username">Username:</label>
        <input class="login_Input" name="username" id="username" type="text" placeholder="Username" required><br><br>
        <label class="label_register" for="password">Password:</label>
        <input class="login_Input" name="password" id="password" type="text" placeholder="Password" required><br><br>
        <label class="label_register" for="name">Name:</label>
        <input class="login_Input" name="name" id="name" type="text" placeholder="Name" required><br><br>
        <label class="label_register" for="email">Email:</label>
        <input class="login_Input" name="email" id="email" type="text" placeholder="Email" required><br><br>

        <p>All field are required to successfully register!</p>
        <button class="button" type="submit" value="login">Register</button>
        <a href="index.php">Back</a>
    

    
    </form>
</main>
    
</body>
</html>