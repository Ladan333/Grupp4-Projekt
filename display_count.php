<?php 
if(session_status()==PHP_SESSION_NONE) {session_start();}
            require_once('PDO.php');
       
            $user_id = $_SESSION['id']; 

            $stmt = $pdo->prepare("
                SELECT COUNT(respond) AS unread_count 
                FROM dm 
                WHERE respond = 1 AND responder_id = :user_id
            ");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['display_count'] = $result['unread_count'] ?? 0;
            ?>
            