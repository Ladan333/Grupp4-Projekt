<?php
require_once "../Entity/userEntity.php";

class UserController
{
    private $dao;
    private $pdo;
    public function __construct($pdo)
    {

        $this->pdo = $pdo;
        $this->dao = new UserDAO($pdo);
    }



    public function changeOrNot($first_name, $last_name, $email, $profileContent, $imageBase64, $user_id)
    {
        $source = $_POST['source'] ?? '/Views/edituser.php';
        $user = $_SESSION['user'];
        $userid = $user->getId();
        if (!empty($_FILES['profile_image']['tmp_name'])) {
            $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
            $imageBase64 = base64_encode($imageData);  //konvertering av bild

            if (empty($email)) {
                die("Error: Email field cannot be empty!");
            }
            $this->dao->dontChangePicture($first_name, $last_name, $email, $profileContent, $imageBase64, $user_id);
        } else {
            $this->dao->changePicture($first_name, $last_name, $email, $profileContent, $user_id);
        }

        // skicka tillbaks till rätt sida beroende på vart du ändrar någons uppgifter. Ändrar du dig själv i adminpanelen så kommer du till din egen profil
        if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['id'] != $userid) {
            header("Location: ../Views/admin_list.php");
        } else {
            header("Location: ../views/profile.php");
        }
        exit;

        //bildhantering sparad via den globala variabeln $_FILES
    }

    public function adminDeleteUser($user_id)
    {
        if (!isset($_POST['id'])) {
            $_SESSION['message'] = "Ingen användare vald.";
            header("Location: ../Views/admin_list.php");
            exit;
        }

        $user = $this->dao->getUserById($user_id);

        if (!$user) {
            $_SESSION['message'] = "Användaren hittades inte.";
            header("Location: ../Views/admin_list.php");
            exit;
        }

        $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);

        // Radera användaren
        $this->dao->DeleteUserById($user_id);

        // Spara meddelande för users.php
        $_SESSION['message'] = "Användaren $full_name är nu borttagen.";

        // Skicka tillbaka till users.php
        header("Location: ../Views/admin_list.php");
        exit;
    }



    public function delete()
    {
        //Delete post - ligger i blogwall
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
            $post_id = $_POST['post_id'];

            $deleteBlogPost = $this->dao->DeleteBlogPostBy($post_id);

            if ($deleteBlogPost) {
                $_SESSION['success'] = 'Post deleted successfully!';
                header("Location: ../Views/blogwall.php ");
                exit();
            } else {
                $_SESSION['error'] = "You dont have permission to delete this post";
                header("Location: ../Views/blogwall.php");
                exit();
            }

        }

        //Delete user - ligger i edituser.php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletes'])) {
            $user = (int) $_POST['deletes'];

            if (!empty($user)) {

                $stmt = $this->dao->DeleteUserById($user);

                if ($stmt) {

                    $_SESSION['success'] = 'User deleted succesful';
                    unset($_SESSION[""]);
                    session_destroy();
                    setcookie(session_name(), '', time() - 3600, '/');
                    header("Location: ../Views/index.php");


                    exit();
                } else {
                    $_SESSION["error"] = "Failed";
                }
            } else {
                $_SESSION["error"] = "Invalid username";
                header("../Views/edituser.php");
                exit();
            }


        }

}
}
?>