<?php
$cookie_name = "user_session";
$cookie_value = session_id();
$cookie_time = time() + 3600;

setcookie($cookie_name, $cookie_value, $cookie_time, "/", "", false, true);

session_start();
$username = $_SESSION['username'] ?? 'Username';
$isAdmin = $_SESSION["role"] ?? false;


require_once 'PDO.php';


if( isset($_GET['user_name'])){

$user = $_GET['user_name'];
$statement = $pdo->prepare("SELECT id FROM users WHERE user_name = :user");
$statement->bindParam(":user", $user, PDO::PARAM_STR);
$statement->execute();
$usersId = $statement->fetch(PDO::FETCH_ASSOC);
$userId = $usersId['id'] ?? null; //Konverterar från array till int

if(!$userId){
    header('Location: messages.php'); //Någon kontroll här kanske - vad ska vi göra om GET inte skickats? Kommer bli tomt på sidan ifall vi skulle kört vidare.. redirect nu bara.. kanske returnera något att displaya på redirecten?
    exit();
}



    $stmt = $pdo->prepare("SELECT d.message_image, d.message_content, d.CreatedDate, u.user_name
FROM dms as d JOIN users as u on d.user1_id = u.id OR d.user2_id = u.id
WHERE (d.user1_id = :thisUser AND d.user2_id = :user) OR (d.user1_id = :user AND d.user2_id = :thisUser)
ORDER BY d.CreatedDate ASC");
$stmt->bindParam(":user", $userId, PDO::PARAM_INT);
$stmt->bindParam(":thisUser", $_SESSION['id'], PDO::PARAM_INT);

$stmt->execute();
$result = $stmt->fetchall(PDO::FETCH_ASSOC);

}
else{

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
<?php require "navbar.php"; ?>


    <main class="index">

    <form action="AddComments.php" id="addComments-form" method="POST">

<input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">
<input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

<textarea class="comment-input" name="comment_input"  cols="40" placeholder="message" required></textarea>

<button class="comment-btn" type="submit">Send message</button>
</form>

    <?php foreach ($result as $results): ?>
        
                            <div class="results">
                                <span id="user">
                                    <strong><a
                                    href="profile.php?user_name=<?= urlencode($results['user_name']) ?>" class="profile-link">
                                    <?= "&nbsp;&nbsp;" . htmlspecialchars(ucwords(strtolower($results['user_name']))) ?>
                                    <?php $profile_img = !empty($results['message_image']) ? "data:image/png;base64," . htmlspecialchars($results['message_image']) : "./files/no_picture.jpg"; ?>
                                    <img src="<?= $profile_img ?>" alt="./files/no_picture.jpg" width="30" height="30"
                                        style="border-radius:50%;">
                                        </a></strong>
                                </span>
                                <?php echo htmlspecialchars($results['message_content']); ?>
                                <p><?php echo htmlspecialchars($results['CreatedDate']) ?></p>
                                <?php if ($isAdmin == $_SESSION['id']): ?>
                        <!-- Only allow the user who created the post or admins to delete -->
                        <!-- <form action="delete_results.php" method="POST" style="display: inline;">
                            <input type="hidden" name="delete_results" value="<?php echo $results['id']; ?>">
                            <button type="submit" class="delete-btn">X</button>
                        </form> -->

                    <?php endif; ?>

    <?php endforeach; ?>

       
    </main>

</body>

</html>