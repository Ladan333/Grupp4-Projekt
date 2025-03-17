<?php

class FollowDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllFollowsByUserId($id)
    {

        $stmt = $this->pdo->prepare("SELECT follow_id FROM follows WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function getallFollows($id, $profile_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id");
        $stmt->bindParam(":user_id", $id);
        $stmt->bindParam(':follow_id', $profile_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showFollowers($id)
    {
        $query = "SELECT u.id, u.first_name, u.last_name, u.user_name
          FROM follows f
          JOIN users u ON f.follow_id = u.id
          WHERE f.user_id = :id
          ORDER BY u.first_name DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function follow($id, $profile_id){
        
        $stmt = $this->pdo->prepare('SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id');
        $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":follow_id", $profile_id, PDO::PARAM_INT);
        $stmt->execute();
          
        if ($stmt->rowCount() > 0) {
            return false; 
        }
    
        $stmt = $this->pdo->prepare('INSERT INTO follows (user_id, follow_id) VALUES (:user_id, :follow_id)');
        $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
        $stmt->bindParam(':follow_id', $profile_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;  
        }
    
        return false;  
    }
    public function unfollow($id, $profile_id){
       
        $stmt = $this->pdo->prepare('SELECT * FROM follows WHERE user_id = :user_id AND follow_id = :follow_id');
        $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
        $stmt->bindParam(':follow_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
    
      
        if ($stmt->rowCount() == 0) {
            return false;
        }
    
        $stmt = $this->pdo->prepare('DELETE FROM follows WHERE user_id = :user_id AND follow_id = :follow_id');
        $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
        $stmt->bindParam(':follow_id', $profile_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;  
        }
    
        return false;  
    }

}