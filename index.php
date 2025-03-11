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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT id, pwd, role FROM users WHERE user_name = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result_userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $hashwed_password = $result_userinfo['pwd'];


    if (!$result_userinfo) {
        echo "invalid";
    } else if (password_verify($password, $hashwed_password)) {
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


    <main class="index">
        <div class="form-container">
            <div class="flip-card" id="flipCard">

                <!-- Login Form -->
                <form class="form-side login" action="index.php" method="POST">
                    <h2>Welcome to <br>The Wall</h2>
                    <label for="username">Username</label>
                    <input class="login_Input" name="username" id="username" type="text" placeholder="Input username" required>

                    <label for="password">Password</label>
                    <input class="login_Input" name="password" id="password" type="password" placeholder="Input password" required>

                    <button class="button" type="submit">Login</button>
                    <a class="forgotpass" href="forg" id="forgotPassLink">Forgot password?</a>
                    <p class="pindex">No account?</p><a href="register.php" id="registerLink">Register here</a>
                </form>

                <!-- Forgot Password Form -->
                <form class="form-side forgot-password" action="forgotpassword.php" method="POST">
                    <h2>Forgot Password?</h2>
                    <label for="username">Username</label>
                    <input class="email_Input" name="username" id="username" type="text" placeholder="Enter your username" required>

                    <label for="email">Email</label>
                    <input class="email_Input" name="email" id="email" type="text" placeholder="Enter your email" required>

                    <button class="button" type="submit">Send Reset Link</button>
                    <a href="index.php" id="backToLogin">Back to Login</a>
                </form>

                <!-- Register Form -->
                <form class="form-side register" action="register.php" method="POST">
                    <label class="label_register" for="username">Username:</label>
                    <input class="login_Input" name="username" id="username" type="text" placeholder="Username" required>
                    <label class="label_register" for="password">Password:</label>
                    <input class="login_Input" name="password" id="password" type="text" placeholder="Password" required>
                    <label class="label_register" for="first_name">First name:</label>
                    <input class="login_Input" name="first_name" id="first_name" type="text" placeholder="First_name" required>
                    <label class="label_register" for="last_name">Last name:</label>
                    <input class="login_Input" name="last_name" id="last_name" type="text" placeholder="Last_name" required>
                    <label class="label_register" for="email">Email:</label>
                    <input class="login_Input" name="email" id="email" type="text" placeholder="Email" required>

                    <p>All field are required to successfully register!</p>
                    <div class="g-recaptcha" data-sitekey="6LdPze8qAAAAACHaleZLBJzS1bXV-oWHmIdfSf9I"></div>
                    <br>
                    <button class="button" type="submit" value="login">Register</button>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

                    <a href="index.php" id="backToLoginPage">Back</a>



                </form>
            </div>
        </div>
    </main>
    <script>
        document.getElementById("forgotPassLink").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("flipCard").classList.add("flipped");
            document.querySelector(".login").style.transform = "rotateY(0deg)";
            document.querySelector(".register").style.display = 'none';
            document.querySelector(".forgot-password").style.display = 'flex';
        });

        document.getElementById("backToLogin").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("flipCard").classList.remove("flipped");
            document.querySelector(".login").style.transform = "rotateY(0deg)";

        });

        document.getElementById("registerLink").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("flipCard").classList.add("flipped");
            document.querySelector(".login").style.transform = "rotateY(0deg)";
            document.querySelector(".forgot-password").style.display = 'none';
            document.querySelector(".register").style.display = 'flex';
        });

        document.getElementById("backToLoginPage").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("flipCard").classList.remove("flipped");
            document.querySelector(".login").style.transform = "rotateY(0deg)";

        });
    </script>
</body>

</html>