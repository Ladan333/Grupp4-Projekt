<?php

require_once "../Entity/userEntity.php";
session_start();
require '../Views/navbar.php';
require_once '../övrigt/PDO.php';
require "../Dao/UserDAO.php";
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
require './PHPMailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $UserDao = new UserDAO($pdo);


    $newPassword = $UserDao->forgottPassword($username, $email);

    if ($newPassword) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'send.one.com';  
            $mail->SMTPAuth = true;
            $mail->Username = 'no-reply@theblogwall.se';  
            $mail->Password = 'Blog123456';  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom('no-reply@theblogwall.se', 'The Blog Wall');
            $mail->addAddress($email, $username);  

            $mail->isHTML(true);
            $mail->Subject = "Ditt nya lösenord";
            $mail->Body    = "<p>Hej <strong>{$username}</strong>,</p>
                              <p>Ditt nya lösenord är: <strong>{$newPassword}</strong></p>
                              <p>Logga in här: <a href='https://theblogwall.se'>The Blog Wall</a></p>
                              <p>Vänligen ändra ditt lösenord så snart som möjligt under profilen!</p>";
            $mail->AltBody = "Hej {$username},\n\nDitt nya lösenord är: {$newPassword}\n\n";

            if ($mail->send()) {
                echo "Ett nytt lösenord har skickats till din e-post.";
            } else {
                echo "Det gick inte att skicka e-post. Kontakta support.";
            }

            echo "<br><a href='../Views/index.php'>Logga in med ditt nya lösenord</a>";

        } catch (Exception $e) {
            echo "E-post kunde inte skickas. Fel: {$mail->ErrorInfo}";
        }
    } else {
        echo "Användarnamn eller e-postadress hittades inte.";
    }
}




?>




<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glömt lösen</title>
</head>

<body>

    <form class="check-email" action="" method="POST">
        <h2>Welcome to <br>The Wall</h2>
        <label for="email">Username</label>
        <input class="email_Input" name="username" id="username" type="text" placeholder="Username" required>
        <label for="email">Username</label>
        <input class="email_Input" name="email" id="email" type="text" placeholder="Email" required>
        <br>

        <button class="button" type="submit" value="skicka">Skicka</button>

    </form>



</body>

</html> -->