<?php
require_once '../Entity/userEntity.php';

class UserDAO{
    private $pdo; 

    public function __construct($pdo){
        $this->pdo = $pdo;
}

public function DeleteBlogPostBy($post_id)
{
    $stmt = $this->pdo->prepare("DELETE FROM blogposts WHERE id = :post_id ");
    $stmt->bindParam(':post_id', $post_id);
    if(!$stmt->execute()){
        return false;
    }
   
     
}

public function DeleteUserById($user_id)
{
    $deleteStmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$user_id]);
    
}
public function getUserById($user_id)
{
$stmt = $this->pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
return $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

public function changePicture($first_name, $last_name, $email, $profileContent, $user_id)
{
    $stmt = $this->pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profileContent = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $profileContent, $user_id]);
    
}
public function dontChangePicture($first_name, $last_name,  $email, $profileContent ,$imageBase64, $user_id)
{
    $stmt = $this->pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profileContent = ?, profile_image = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name,  $email, $profileContent ,$imageBase64, $user_id]);
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
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    if($userInfo){
        return new User(
            $userInfo["id"],
            $userInfo["first_name"],
            $userInfo["last_name"],
            $userInfo["user_name"],
            $userInfo["pwd"],
            $userInfo["email"],
            $userInfo["role"],
            $userInfo["profileContent"],
            $userInfo["profile_image"],
            createdDate: $userInfo["CreatedDate"]

        );
    }

}

public function getUserByUserByNameForProfile($username){
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

public function getProfilePicture($username) 
{
    $stmt = $this->pdo->prepare("SELECT profile_image FROM users WHERE user_name = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

public function searchUsersByLikeNameOrEmail($search)
{
    // SQL-fråga för att hämta användare (filtrerar om vi söker på något)
$stmt = $this->pdo->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $result;
}

public function changeRole($isAdmin, $user_id)
{
    $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$isAdmin, $user_id]);

}

public function searchUsersByNameOrUsername($searchUser){
    $stmt = $this->pdo->prepare("
    SELECT * FROM users 
    WHERE user_name LIKE :searchuser 
    OR first_name LIKE :searchfirst 
    OR last_name LIKE :searchlast
    ");    $searchUser = "%" . $searchUser . "%";
    
    $stmt->bindParam(":searchuser", $searchUser, PDO::PARAM_STR);
    $stmt->bindParam(":searchfirst", $searchUser, PDO::PARAM_STR);
    $stmt->bindParam(":searchlast", $searchUser, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
}
?>

