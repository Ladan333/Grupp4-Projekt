<?php
session_start();
require 'PDO.php';

// Hämta id från URL
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Ingen användare vald.";
    header("Location: admin_list.php");
    exit;
}

$user_id = $_GET['id'];

// Hämta användarens namn för att visa i bekräftelsen
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['message'] = "Användaren hittades inte.";
    header("Location: admin_list.php");
    exit;
}

$full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <title>Bekräfta borttagning</title>
</head>
<body>

<h1>Bekräfta borttagning</h1>
<p>Är du säker på att du vill ta bort användaren <strong><?= $full_name ?></strong>?</p>

<form method="POST" action="admin_delete_user.php">
    <input type="hidden" name="id" value="<?= $user_id ?>">
    <button type="submit">Ja, ta bort</button>
    <a href="users.php">Nej, gå tillbaka</a>
</form>

</body>
</html>


