<?php

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