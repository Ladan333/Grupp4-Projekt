<?php
// Inkluderar databaskopplingen
require '../db.php';

// Kollar om en sökning har gjorts (via URL)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL-fråga för att hämta användare (filtrerar om vi söker på något)
$query = "SELECT * FROM users WHERE name LIKE :search OR email LIKE :search";
$stmt = $pdo->prepare($query);
$stmt->execute(['search' => "%$search%"]);

// Hämtar en lista av användare
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Adminpanel - Hantera användare och deras inlägg</h1>

<!-- Sökfält för att hitta användare -->
<form method="GET">
    <input type="text" name="search" placeholder="Sök efter namn eller email" value="<?=htmlspecialchars($search)?>">
    <button type="submit">Sök</button>
</form>

<!-- Lista på alla användare -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>Namn</th>
        <th>Email</th>
        <th>Roll</th>
        <th>Hantera inlägg</th> 
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <!-- Knappar för att hantera användarens inlägg /Implementera användare istället för kommentarer koppla till blogwall / redigera användarnamn-->
                <a href="edit_posts.php?user_id=<?= $user['id'] ?>">Redigera inlägg</a> |
                <a href="delete_posts.php?user_id=<?= $user['id'] ?>" onclick="return confirm('Är du säker på att du vill ta bort ALLA inlägg för denna användare?')">Ta bort alla inlägg</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="posts.php">Gå till fullständig inläggshantering</a>

