<?php
// Starta session för att kunna kolla vem som är inloggad
session_start();

// Inkluderar databaskopplingen
require "PDO.php";

// Kolla om användaren är inloggad och har rollen "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    die("Du måste vara admin för att se denna sida."); // Enkel säkerhet
}

// Kollar om en sökning har gjorts (via URL)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL-fråga för att hämta användare (filtrerar om vi söker på något)
$query = "SELECT * FROM users WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);

// Hämtar en lista av användare
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <!-- <meta http-equiv="refresh" content="2"> -->
    <title>Adminpanel - Hantera användare och deras inlägg</title>
</head>

<body>
    <?php require "navbar.php"; ?>
    <main class="main-admin_list">
        <h1>Adminpanel - Hantera användare och deras inlägg</h1>

        <!-- Sökfält för att hitta användare baserat på namn eller email -->
        <form method="GET">
            <input class="searchbar-admin" type="text" name="search" placeholder="Sök efter namn eller email"
                value="<?= htmlspecialchars($search) ?>">
            <!-- <button class="btn-search-admin" type="submit">Sök</button> -->
        </form>

        <!-- Tabell där vi listar alla användare med information -->
        <table >
            <tr class="table-header">
                <th>ID</th>
                <th>Förnamn</th>
                <th>Efternamn</th>
                <th>Email</th>
                <th>Admin*</th>
                <th>Redigera</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr class="table-header">
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <div class="edit-buttons">
                            <a class="edit_user" href="edituser.php?id=<?= $user['id'] ?>">Redigera</a>
                            <a class="delete_user" href="delete_user.php?id=<?= $user['id'] ?>"
                                onclick="return confirm('Är du säker på att du vill ta bort denna användare?')">Ta bort</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
                <p>* 1 = Admin, 0 = Användare</p>
        <!-- Länk till en separat sida för att hantera alla inlägg -->
        <a class="link_to_blogwall" href="posts.php">Gå till inläggshantering</a>

        <!-- Länk för att skapa ny användare om admin vill -->
        <br><br>
        <!-- <a href="create_user.php">Skapa ny användare</a> -->
    </main>
</body>

</html>