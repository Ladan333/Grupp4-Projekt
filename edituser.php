<?php
// Startar session för att veta vilken användare som är inloggad
session_start();

require "PDO.php";// Kopplar till databasen

// Kollar om användaren är inloggad, skickas till login
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}


// Hämtar användarens data från databas
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];

//Gör SQL-fråga för att hämta inloggad användare
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $user_id]); 
$user = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($user);
// Om formuläret har skickats (POST), uppdatera användarinfo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $profileContent = $_POST['profileContent'];

    // Hantera uppladdning av ny profilbild om användaren har valt fil
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['profile_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Kolla om det är en giltig bildfil
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            // Flytta filen till uploads-mappen
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath);

            // Uppdatera databasen med den nya bildfilens namn
            $stmt = $pdo->prepare("UPDATE users SET name = ?, profileContent = ?, profile_image = ? WHERE id = ?");
            $stmt->execute([$name, $profileContent, $fileName, $user_id]);
        } else {
            echo "Fel: Endast JPG, JPEG, PNG eller GIF tillåtet.";
            exit;
        }
    } else {
        // Uppdatera bara textinfo
        $stmt = $pdo->prepare("UPDATE users SET name = ?, profileContent = ? WHERE id = ?");
        $stmt->execute([$name, $profileContent, $user_id]);
    }

    // Vid "spara" - tillbaka till profil
    if (isset($_POST['save'])) {
        header("Location: profile.php");
        exit;
    }
    // HTML och formulär för redigering börjar här
}?>
