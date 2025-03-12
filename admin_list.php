<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Starta session för att kunna kolla vem som är inloggad
session_start();

// Inkluderar databaskopplingen
require 'PDO.php';

// Kolla om användaren är inloggad och har rollen "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    header("Location: index.php"); // Enkel säkerhet
}

// Kollar om det finns ett meddelande från tidigare action (t.ex. borttagning av användare)
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Kollar om en sökning har gjorts (via URL)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL-fråga för att hämta användare (filtrerar om vi söker på något)
$query = "SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);

// Hämtar en lista av användare
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <title>Adminpanel - Hantera användare och deras inlägg</title>
</head>

<body>
    <?php require "navbar.php"; ?>
    <main class="main-admin_list">
        <h1>Edit page</h1>

        <!-- Visar meddelande om det finns något -->
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Sökfält för att hitta användare baserat på namn eller email -->
        <form method="GET">
            <input class="searchbar-admin" type="text" name="search" placeholder="Search for a name or email"
                value="<?= htmlspecialchars($search) ?>">
        </form>

        <!-- Tabell där vi listar alla användare med information -->
        <table>
            <tr class="table-header">
                <th>ID</th>
                <th>First name</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Edit</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr class="table-header">
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><div class="edit-buttons">
                        <?php if ($user['role'] == 0)  { ?>
                            <a class="edit_user_admin" href="is_admin.php?role=<?= $user['role'] ?>&id=<?= $user['id'] ?>">Make admin</a>

                        <?php } else { ?>
                            <a class="delete_user" href="is_admin.php?role=<?= $user['role'] ?>&id=<?= $user['id'] ?>">Delete admin</a>

                            <?php } ?>
                            </div>
                    </td>
                    <td>
                        <div class="edit-buttons">
                            <a class="edit_user" href="edituser.php?id=<?= $user['id'] ?>">Edit</a>
                            <a class="delete_user" href="admin_delete_confirm.php?id=<?= $user['id'] ?>">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
       
        <!-- Länk till en separat sida för att hantera alla inlägg -->
        <a class="link_to_blogwall" href="blogwall.php">To posts</a>

        <!-- Länk för att skapa ny användare om admin vill -->
        <br><br>
        <!-- <a href="create_user.php">Skapa ny användare</a> -->
    </main>
</body>

</html>
