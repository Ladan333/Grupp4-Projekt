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
//var_dump($user);
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
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 30px;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(82, 0, 0, 0.1);
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgb(91, 91, 91);
        }
        .form-control, .btn {
            border-radius: 8px;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Edit Profile</h2>
        <form action="edituser.php" method="post" enctype="multipart/form-data">
            <div class="text-center mb-3">
                <img src="./files/no_picture.jpg" alt="Profile picture" class="profile-img">
            </div>
            <div class="mb-3">
                <label for="user_name" class="form-label">Username</label>
                <input type="text" id="user_name" name="user_name" class="form-control bg-dark text-light" required>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control bg-dark text-light" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control bg-dark text-light" required>
            </div>
            <div class="mb-3">
                <label for="profileContent" class="form-label">Bio</label>
                <textarea id="profileContent" name="profileContent" class="form-control bg-dark text-light" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="profile_image" class="form-label">Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" class="form-control bg-dark text-light">
            </div>
            <div class="text-center">
                <button type="submit" name="save" class="btn btn-primary">Save Changes</button>
                <a href="profile.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>