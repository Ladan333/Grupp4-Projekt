<?php

session_start();
require 'navbar.php';
require_once 'PDO.php';

require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
require './PHPMailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];


    $stmt = $pdo->prepare("SELECT id, user_name, email FROM users WHERE user_name = :username AND email = :email");

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result_userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result_userinfo) {

        function generatepass($lengt = 8)
        {

            $randomstring = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!";
            return substr(str_shuffle($randomstring), 0, $lengt);
        }
        $password = generatepass();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $update_stmt = $pdo->prepare("UPDATE users SET pwd = :password WHERE id = :id");
        $update_stmt->bindParam(":password", $hashed_password);
        $update_stmt->bindParam(":id", $result_userinfo['id']);
        if ($update_stmt->execute()) {
            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host = 'send.one.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'no-reply@theblogwall.se';
                $mail->Password = 'Blog123456';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;


                $mail->setFrom('no-reply@theblogwall.se', 'The Blog Wall');
                $mail->addAddress($result_userinfo['email'], $result_userinfo['user_name']);
             


                $mail->isHTML(true);
                $mail->Subject = "Ditt nya lösenord";
                $mail->Body = "<p>Hej <strong>{$result_userinfo['user_name']}</strong>,</p>
                                <p>Ditt nya lösenord är: <strong>$password</strong></p>
                                <p>Logga in här: <a href='https://theblogwall.se'>The Blog Wall</a></p>
                                <p>Vänligen ändra ditt lösenord så snart som möjligt under profilen!</p>";
                $mail->AltBody = "Hej {$result_userinfo['user_name']},\n\nDitt nya lösenord är: $password\n\n";


                if ($mail->send()) {
                    echo "Ett nytt lösenord har skickats till din e-post.";
                } else {
                    echo "Det gick inte att skicka e-post. Kontakta support.";
                }

                echo "<br><a href='index.php'>Logga in med ditt nya lösenord</a>";

            } catch (Exception $e) {
                echo "E-post kunde inte skickas. Fel: {$mail->ErrorInfo}";
            }
        }

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