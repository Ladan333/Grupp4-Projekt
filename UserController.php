<?php
// require 'userDAO.php';

class UserController{
    private $dao;
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->dao = new UserDAO($pdo);
    }



public function changeOrNot($first_name, $last_name,  $email, $profileContent ,$imageBase64, $user_id)
{

    if (!empty($_FILES['profile_image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
        $imageBase64 = base64_encode($imageData);  //konvertering av bild
    
        if (empty($email)) {
            die("Error: Email field cannot be empty!");
        }
        $this->dao->dontChangePicture($first_name, $last_name,  $email, $profileContent ,$imageBase64, $user_id);
    } else {
        $this->dao->changePicture($first_name, $last_name, $email, $profileContent, $user_id);
    
    }
    
    // skicka tillbaks till rätt sida beroende på vart du ändrar någons uppgifter. Ändrar du dig själv i adminpanelen så kommer du till din egen profil
    if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] != $_SESSION['id']) {
        header("Location: admin_list.php");
    } else {
        header("Location: profile.php");
    }
    exit;

    //bildhantering sparad via den globala variabeln $_FILES
}

public function adminDeleteUser($user_id)
{
    if (!isset($_POST['id'])) {
        $_SESSION['message'] = "Ingen användare vald.";
        header("Location: admin_list.php");
        exit;
    }

$user = $this->dao->getUserById($user_id);

    if (!$user) {
        $_SESSION['message'] = "Användaren hittades inte.";
        header("Location: admin_list.php");
        exit;
    }
    
    $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
    
    // Radera användaren
    $this->dao->DeleteUserById($user_id);

    // Spara meddelande för users.php
    $_SESSION['message'] = "Användaren $full_name är nu borttagen.";
    
    // Skicka tillbaka till users.php
    header("Location: admin_list.php");
    exit;
}




}

?>