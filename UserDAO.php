<?php
class UserDAO{
    private $pdo; 

    public function __construct($pdo){
        $this->pdo = $pdo;
}

public function findUserWhoWantToChangePassword($user_id)
{
    $stmt = $this->pdo->prepare("SELECT pwd FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function changePassword($new_password, $user_id)
{
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("UPDATE users SET pwd = ? WHERE id = ?");
   return $stmt->execute([$hashed_password, $user_id]);
}

public function getUserByUserName($username){
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_name = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);

}

public function registerUser($username, $password,$email, $first_name, $last_name){
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $this->pdo->prepare("INSERT INTO users (user_name, pwd, first_name, last_name, email) VALUES (:username, :password, :first_name, :last_name, :email)");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":first_name", $first_name);
    $stmt->bindParam(":last_name", $last_name);
    $stmt->execute();

}


public function checkIfUserExists($username, $email){
    $stmt = $this->pdo->prepare("SELECT user_name FROM users WHERE user_name = :username OR email = :email");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    return  $stmt->fetch(PDO::FETCH_ASSOC);

}

public function checkUsernameAndEmail($username, $email)
{
    $stmt = $this->pdo->prepare("SELECT id, user_name, email FROM users WHERE user_name = :username AND email = :email");

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);  
}
public function forgottPassword($username, $email)
{
    
    $userinfo = $this->checkUsernameAndEmail($username, $email);
    if (!$userinfo) {
       
        return false;
    }

    
    $randomstring = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!";
    $password = substr(str_shuffle($randomstring), 0, 8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $update_stmt = $this->pdo->prepare("UPDATE users SET pwd = :password WHERE id = :id");
    $update_stmt->bindParam(":password", $hashed_password);
    $update_stmt->bindParam(":id", $userinfo['id']);
    
    if ($update_stmt->execute()) {
        return $password;  
    }
    
    return false;  
}
}
?>

