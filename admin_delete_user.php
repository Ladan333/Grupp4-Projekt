<?php
session_start();
require 'PDO.php';

// Kontrollera om id har skickats via POST
if (!isset($_POST['id'])) {
    $_SESSION['message'] = "Ingen användare vald.";
    header("Location: admin_list.php");
    exit;
}

$user_id = $_POST['id'];
// Hämta användarprofil innan borttagning
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['message'] = "Användaren hittades inte.";
    header("Location: admin_list.php");
    exit;
}

$full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);

// Radera användaren
$deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->execute([$user_id]);

// Spara meddelande för users.php
$_SESSION['message'] = "Användaren $full_name är nu borttagen.";

// Skicka tillbaka till users.php
header("Location: admin_list.php");
exit;
