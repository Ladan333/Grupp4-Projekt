<?php

class DmDAO
{

    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;

    }

    public function getMessages($user_id)
    {
        $stmt = $this->pdo->prepare("
    SELECT dms.id AS message_id,

           dms.message_content,
           dms.CreatedDate,
           dms.unread_status,
           dms.user1_id,
           dms.user2_id,
           user1.*,
           user2.*,
           CASE 
               WHEN dms.user1_id = :user_id THEN user2.user_name 
               ELSE user1.user_name 
           END AS conversation_partner
    FROM dms
    JOIN users user1 ON user1.id = dms.user1_id
    JOIN users user2 ON user2.id = dms.user2_id
    WHERE dms.id IN (
        SELECT MAX(id) FROM dms 
        WHERE user1_id = :user_id1 OR user2_id = :user_id2
        GROUP BY LEAST(user1_id, user2_id), GREATEST(user1_id, user2_id)
    )
    ORDER BY dms.CreatedDate DESC
");

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function unreadMessages($user_id)
    {
        $unreadStmt = $this->pdo->prepare("
    SELECT COUNT(DISTINCT user1_id) AS unread_count 
    FROM dms 
    WHERE unread_status = 1 
    AND user2_id = :user_id
");
        $unreadStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $unreadStmt->execute();
        return $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

    }

    public function idOtherUser($other_user_name)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE user_name = :user_name");
        $stmt->bindParam(':user_name', $other_user_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStmt($user_id, $other_user_id)
    {
        $updateStmt = $this->pdo->prepare("
    UPDATE dms 
    SET unread_status = 0 
    WHERE unread_status = 1 
    AND user2_id = :user_id 
    AND user1_id = :other_user_id
");

        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
        $updateStmt->execute();
        if (!$updateStmt) {
            return false;
        }


    }
    public function insertMessages($message_content, $message_image, $user_id, $other_user_id, $true)
    {

        $stmt = $this->pdo->prepare("
        INSERT INTO dms (unread_status, message_content, message_image, CreatedDate, user1_id, user2_id)
        VALUES (:true, :message_content, :message_image, NOW(), :user_id, :other_user_id)");
        $stmt->bindParam(':message_content', $message_content, PDO::PARAM_STR);
        $stmt->bindParam(':message_image', $message_image, PDO::PARAM_LOB);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':true', $true, PDO::PARAM_INT);

        // var_dump($insertcheck);


        if (!$stmt->execute()) {
            die("FAILED TO SEND ");
        } else {
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

    }

    public function getConversation($user_id, $other_user_id)
    {
        $stmt = $this->pdo->prepare("
SELECT 
    dms.id AS message_id,
    dms.message_content,
    dms.message_image,
    dms.CreatedDate,
    dms.unread_status,
    dms.user1_id,
    dms.user2_id,
    user1.user_name AS user1_name,  -- Hämtar användarnamn för user1
    user2.user_name AS user2_name   -- Hämtar användarnamn för user2
FROM dms
JOIN users user1 ON user1.id = dms.user1_id  
JOIN users user2 ON user2.id = dms.user2_id  
WHERE 
    (dms.user1_id = :user_id1 AND dms.user2_id = :other_user_id1)
    OR (dms.user1_id = :other_user_id2 AND dms.user2_id = :user_id2)
ORDER BY dms.CreatedDate ASC;
");

        // Bind parametrarna
        $stmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':other_user_id1', $other_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':other_user_id2', $other_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

}




