<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
    die("Du måste vara admin för att se denna sida."); // Enkel säkerhet
}
require"PDO.php";

$stmt = $pdo ->prepare("SELECT * FROM users");
$stmt -> execute();
$users = $stmt ->fetchAll(PDO::FETCH_ASSOC);

foreach($users as $user) {
    foreach($user as $key => $value) {
        echo $key . ": " . $value . "<br>";
    }
}
?>