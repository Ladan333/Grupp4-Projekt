<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

// var_dump($_POST);
// Startar session för att veta vilken användare som är inloggad
session_start();
// Get the user ID from URL or default to logged-in user


require 'PDO.php'; // Kopplar till databasen
require 'userDAO.php';
require 'UserController.php';

// Kollar om användaren är inloggad, skickas till login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
if (isset($_SESSION['password_updated'])) {
    echo '<div class="alert alert-success">' . $_SESSION['password_updated'] . '</div>';
    unset($_SESSION['password_updated']);
} elseif (isset($_SESSION['password_error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['password_error'] . '</div>';
    unset($_SESSION['password_error']);
}

// Hämtar användarens data från databas
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die(" Error: User not found.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if (isset($_POST['change_password'])) {
        $user_id = $_SESSION['id'];
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];


        // $stmt = $pdo->prepare("SELECT pwd FROM users WHERE id = ?");
        // $stmt->execute([$user_id]);
        // $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //Ersätter koden ovan med Dao
        $userDao = new UserDao($pdo);
        $user = $userDao->findUserWhoWantToChangePassword($user_id);

        if (!$user) {
            $_SESSION['password_error'] = "Error: User not found.";
            header("Location: edituser.php");
            exit();
        }


        if (!password_verify($old_password, $user['pwd'])) {
            $_SESSION['password_error'] = "Incorrect current password.";
            header("Location: edituser.php");
            exit();
        }


        // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


        // $stmt = $pdo->prepare("UPDATE users SET pwd = ? WHERE id = ?");

        //Ersätter koden ovan med Dao
        $changePassword = $userDao->changePassword($new_password, $user_id);

        if ($changePassword) {
            $_SESSION['password_updated'] = "Password updated successfully.";
        } else {
            $_SESSION['password_error'] = "Failed to update password. Try again.";
        }

        header("Location: edituser.php");
        exit();
    }
    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];
    } else {
        $user_id = $_SESSION['id'];
    }


    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $profileContent = $_POST['profileContent'] ?? '';
    $email = $_POST['email'] ?? '';

    //bildhantering sparad via den globala variabeln $_FILES

    //Anropar controller som kör querys i DAO
    $change = new UserController($pdo);
    $change->changeOrNot($first_name, $last_name,  $email, $profileContent ,$imageBase64, $user_id);
    



}
// $user_id = $_GET['id'] ?? $_SESSION['id'];



// HTML och formulär för redigering börjar här
?>
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
            <?php




            $profile_img = !empty($user['profile_image']) ? "data:image/png;base64," . htmlspecialchars($user['profile_image']) : "./files/no_picture.jpg"; ?>
            <div class="text-center mb-3">
                <img src="<?= $profile_img ?>" alt="Profile picture" class="profile-img">
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
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" name="email" class="form-control bg-dark text-light"
                    value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="profileContent" class="form-label">Bio</label>
                <textarea id="profileContent" name="profileContent" class="form-control bg-dark text-light"
                    rows="4"><?= htmlspecialchars($user['profileContent'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="profile_image" class="form-label">Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" class="form-control bg-dark text-light">
            </div>
            <div class="text-center">


            <input type="submit" name="save" class="btn btn-primary" value="Save Changes">

                <a href="profile.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <form action="edituser.php?id=<?= $user_id ?>" method="POST">
            <input type="hidden" name="change_password" value="1">

            <div class="mb-3">
                <label for="old_password" class="form-label">Current Password</label>
                <input type="password" id="old_password" name="old_password" class="form-control bg-dark text-light"
                    required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control bg-dark text-light"
                    required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
        <form action="delete.php" method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="deletes" value="<?php echo $_SESSION['id'] ?>">
            <button type="submit">Delete profile</button>
        </form>


        <script>
            function confirmDelete() {
                return confirm("Delete your account? cant undo this.....");
            }
        </script>
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
        document.addEventListener("DOMContentLoaded", function () {
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
            document.getElementById("start-tour").addEventListener("click", function () {
                driverObj.drive();
            });
        });
    </script>

</body>

</html>