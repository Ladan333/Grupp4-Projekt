<?php
// Startar session för att veta vilken användare som är inloggad
session_start();
// Get the user ID from URL or default to logged-in user


require 'PDO.php'; // Kopplar till databasen

// Kollar om användaren är inloggad, skickas till login
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}


// Hämtar användarens data från databas
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];

//Gör SQL-fråga för att hämta inloggad användare
// if (!isset($_GET['id']) || empty($_GET['id'])) {
//     die("❌ Error: No user ID specified.");
// }

// $user_id = $_GET['id']; // Use ID from URL

// Check if the user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die(" Error: User not found.");
}
//var_dump($user);
// Om formuläret har skickats (POST), uppdatera användarinfo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'] ?? '';  
    $first_name = $_POST['first_name'] ?? ''; 
    $last_name = $_POST['last_name'] ?? '';  
    $profileContent = $_POST['profileContent'] ?? '';
   

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
        $stmt = $pdo->prepare("UPDATE users SET  first_name = ?, last_name = ?, profileContent = ? WHERE id = ?");
        $stmt->execute([ $first_name, $last_name, $profileContent, $user_id]);
    }

    
    if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] != $_SESSION['id']) {
        
        header("Location: admin_list.php");
    } else {
     
        header("Location: profile.php");
    }
    exit;

    
    // HTML och formulär för redigering börjar här
} ?>
<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css" />

    <style>
        body {
            background-color: #121212;
            color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        /* Position the help icon at the bottom right */
        .help-icon {
            position: fixed;
            display: flex;
            align-items: center;
            bottom: 20px;
            right: 20px;
            font-size: 40px;
            width: 50px;
            height: 50px;
            cursor: pointer;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 10px;
            transition: 0.3s ease-in-out;
        }

        .help-icon::before {
            content: "Show Tour";
            position: absolute;
            top: 50%;
            right: 60px;
            /* Positioning tooltip to the left */
            transform: translateY(-50%);
            background-color: black;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }

        /* Show tooltip on hover */
        .help-icon:hover::before {
            opacity: 1;
            visibility: visible;
        }

        .help-icon:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transition: 0.3s ease-in-out;

        }

        ion-icon:hover {
            color: rgb(0, 0, 0);
            transition: 0.3s ease-in-out;

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

        .form-control,
        .btn {
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
        <form action="edituser.php?id=<?= $user_id ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="source" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">

        <div class="text-center mb-3">
                <img src="./files/no_picture.jpg" alt="Profile picture" class="profile-img">
            </div>
            <div class="mb-3">
                <label for="user_name" class="form-label">Username</label>
                <input type="text" id="user_name" name="user_name" class="form-control bg-dark text-light" 
                    value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control bg-dark text-light" 
                    value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control bg-dark text-light" 
                    value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="profileContent" class="form-label">Bio</label>
                <textarea id="profileContent" name="profileContent" class="form-control bg-dark text-light" rows="4"><?= htmlspecialchars($user['profileContent'] ?? '') ?></textarea>
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


    <!-- Help Icon -->
    <div class="help-icon" id="start-tour">
        <ion-icon name="help-circle"></ion-icon>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const driver = window.driver.js.driver;

            const driverObj = driver({
                showProgress: true,
                steps: [{
                        element: ".container",
                        popover: {
                            title: "Edit Profile",
                            description: "Here you can edit your profile details.",
                            side: "left",
                            align: 'start'
                        }
                    },
                    {
                        element: "#user_name",
                        popover: {
                            title: "Username",
                            description: "Update your unique username here.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: "#first_name",
                        popover: {
                            title: "First Name",
                            description: "Update your first name.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: "#last_name",
                        popover: {
                            title: "Last Name",
                            description: "Update your last name.",
                            side: "bottom",
                            align: 'start'
                        }
                    },
                    {
                        element: "#profileContent",
                        popover: {
                            title: "Bio",
                            description: "Write something about yourself.",
                            side: "top",
                            align: 'start'
                        }
                    },
                    {
                        element: "#profile_image",
                        popover: {
                            title: "Profile Picture",
                            description: "Upload a new profile image.",
                            side: "top",
                            align: 'start'
                        }
                    },
                    {
                        element: ".btn-primary",
                        popover: {
                            title: "Save Changes",
                            description: "Click here to save your updated profile.",
                            side: "left",
                            align: 'start'
                        }
                    },
                    {
                        element: ".btn-secondary",
                        popover: {
                            title: "Cancel",
                            description: "Click here to return to the profile page.",
                            side: "right",
                            align: 'start'
                        }
                    },
                ]
            });

            // Start the tour when the help icon is clicked
            document.getElementById("start-tour").addEventListener("click", function() {
                driverObj.drive();
            });
        });
    </script>

</body>

</html>