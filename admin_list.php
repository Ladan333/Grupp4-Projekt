<?php
// Starta session för att kunna kolla vem som är inloggad
session_start();

// Inkluderar databaskopplingen
require"PDO.php";

// Kolla om användaren är inloggad och har rollen "admin"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Du måste vara admin för att se denna sida."); // Enkel säkerhet
}

// Kollar om en sökning har gjorts (via URL)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL-fråga för att hämta användare (filtrerar om vi söker på något)
$query = "SELECT * FROM users WHERE name LIKE :search OR email LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);

// Hämtar en lista av användare
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <title>Adminpanel - Hantera användare och deras inlägg</title>
</head>
<body>

<h1>Adminpanel - Hantera användare och deras inlägg</h1>

<!-- Sökfält för att hitta användare baserat på namn eller email -->
<form method="GET">
    <input type="text" name="search" placeholder="Sök efter namn eller email" value="<?=htmlspecialchars($search)?>">
    <button type="submit">Sök</button>
</form>

<!-- Tabell där vi listar alla användare med information -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>Namn</th>
        <th>Email</th>
        <th>Roll</th>
        <th>Hantera användare</th> 
    </tr>

    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <!-- CRUD-knappar -->
                <a href="edit_user.php?id=<?= $user['id'] ?>">Redigera</a> |
                <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Är du säker på att du vill ta bort denna användare?')">Ta bort</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Länk till en separat sida för att hantera alla inlägg -->
<a href="posts.php">Gå till fullständig inläggshantering</a>

<!-- Länk för att skapa ny användare om admin vill -->
<br><br>
<a href="create_user.php">Skapa ny användare</a>

</body>
</html>

