<?php 
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('PDO.php');

$user_id = $_SESSION['id'] ?? null; 

if ($user_id) {
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user1_id) AS unread_count 
        FROM dms 
        WHERE unread_status = 1 AND user2_id = :user_id
    ");
    
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $fetchcount = $stmt->fetch(PDO::FETCH_ASSOC) ?? ['unread_count' => 0];

    $_SESSION['display_count'] = $fetchcount['unread_count'];
} else {
    $_SESSION['display_count'] = 0; 
}
?>