<?php
session_start();
session_destroy();
require_once "../Entity/userEntity.php";
require "../övrigt/PDO.php";
require "../Dao/UserDAO.php";
session_start();

$cookie_name = "user_session";
$cookie_value = session_id(); 
$cookie_time = time() + 3600; 

setcookie($cookie_name, $cookie_value, $cookie_time, "/", "", false, true);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $recaptcha_secret = "6LdPze8qAAAAAD8w9IMR2K8rnET4AdxIyviyy3z-"; 
    // $recaptcha_response = $_POST['g-recaptcha-response'];

    // //captcha
    // $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    // $response = file_get_contents($verify_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    // $response_data = json_decode($response);
    // if (!$response_data->success) {
    //     echo "reCAPTCHA verification failed. Please try again.<br>";
    //     echo "<a href='index.php'>Back to page</a>";
    //     exit(); 
    // }
    
    
    $user = $_SESSION['user'];
   
    $username = $user->getId();
        
    
    }
    $password = $_POST["password"];
    $email = $_POST["email"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    $userDAO = new UserDAO($pdo);

 
    $checkResult = $userDAO->checkIfUserExists($username, $email); 
    if (!$checkResult) {
        if ($userDAO->registerUser($username, $password, $email, $first_name, $last_name)) {
            $userInfo = $userDAO->getUserByUserName($username);
            if ($userInfo) {
                $_SESSION['user'] = $userInfo;
                $_SESSION['role'] = $userInfo->getRole();
                $_SESSION['login_time'] = time();
                $_SESSION['blogflow'] = 1;
                $_SESSION['sorting'] = 1;

                header("Location: blogwall.php");
                exit();
            }
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        echo "User details already in use";
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


<main class="index">
    <form class="login" action="register.php" method="POST">
        <label class="label_register" for="username">Username:</label>
        <input class="login_Input" name="username" id="username" type="text" placeholder="Username" required><br><br>
        <label class="label_register" for="password">Password:</label>
        <input class="login_Input" name="password" id="password" type="text" placeholder="Password" required><br><br>
        <label class="label_register" for="first_name">First name:</label>
        <input class="login_Input" name="first_name" id="first_name" type="text" placeholder="First_name" required><br><br>
        <label class="label_register" for="last_name">Last name:</label>
        <input class="login_Input" name="last_name" id="last_name" type="text" placeholder="Last_name" required><br><br>
        <label class="label_register" for="email">Email:</label>
        <input class="login_Input" name="email" id="email" type="text" placeholder="Email" required><br><br>

        <p>All field are required to successfully register!</p>
        <!-- <div class="g-recaptcha" data-sitekey="6LdPze8qAAAAACHaleZLBJzS1bXV-oWHmIdfSf9I"></div>  -->
<br>
<button class="button" type="submit" value="login">Register</button>
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->

        <a href="index.php">Back</a>
    

    
    </form>
</main>
    
</body>
</html> -->