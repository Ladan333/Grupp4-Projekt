<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogg";

try {
    $pdo = new Pdo("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    echo "fel" . $e->getMessage();
}