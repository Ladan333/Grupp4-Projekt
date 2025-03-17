<?php

class FollowDAO{
    private $pdo; 

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAllFollowsByUserId($id){

        $stmt = $this->pdo->prepare("SELECT follow_id FROM follows WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
       
    }



}