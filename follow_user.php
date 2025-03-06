<?php

session_start();
require 'PDO.php'; 


if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo ->prepare("SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id");
$stmt->bindParam(":user_id", $_SESSION['id']);
$stmt->bindParam(':follow_id', $_SESSION['follow_id']);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['follow_id'])) {
    if ($_SESSION['follow_id'] != $_SESSION['id'] && empty($results)) {
        $stmt = $pdo -> prepare('INSERT INTO follows (user_id, follow_id) VALUES (:user_id, :follow_id)');
        $stmt->bindParam(":user_id", $_SESSION['id']);
        $stmt->bindParam(':follow_id', $_SESSION['follow_id']);
        $stmt->execute();
    }
    else if ($_GET['user_id'] != $_SESSION['id'] && !empty($results)) {
        $stmt = $pdo -> prepare('DELETE FROM follows WHERE user_id = :user_id AND follow_id = :follow_id');
        $stmt->bindParam(":user_id", $_SESSION['id']);
        $stmt->bindParam(':follow_id', $_SESSION['follow_id']);
        $stmt->execute();
    }
}

$location = "Location: profile.php?user_name=" . $_SESSION['follow_username'];

header($location);
exit();

?>