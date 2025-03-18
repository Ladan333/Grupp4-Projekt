<?php
require_once "userEntity.php";
class PostController {
    private $pdo;
    private $PostDao;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->PostDao = new PostsDAO($pdo);
    }

   
    public function getProfileSortedBlogPosts() {
        $sql = $this->getProfileSortingSql();
        $user = $_SESSION['user'];
        $user_id = $user->getId();
        
        
        $posts = $this->PostDao->getBlogPosts($sql);

 
        if (!empty($posts) && $_SESSION['profile_id'] == $user_id) {
            for ($x = 0; $x <= count($posts); $x++) {  
                if ($posts[$x]["user_id"] != $_SESSION["profile_id"]) {
                    unset($posts[$x]);
                }
            }
        }

        return $posts;
    }

    public function getWallSortedBlogPosts(){
        $sql = $this->getProfileSortingSql();
        $posts = $this->PostDao->getBlogPosts($sql);

        return $posts;
    }

    private function getProfileSortingSql() {
        if ($_SESSION['sorting'] == 1) {
            return "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate, bp.image_base64, bp.user_id
                    FROM blogposts bp
                    JOIN users u ON bp.user_id = u.id
                    ORDER BY bp.CreatedDate DESC";
        } else if ($_SESSION['sorting'] == 2) {
            return "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate, bp.image_base64, bp.user_id, COUNT(c.blog_id)
                    FROM blogposts bp
                    JOIN users AS u ON bp.user_id = u.id 
                    JOIN comments AS c ON c.blog_id = bp.id
                    GROUP BY c.blog_id
                    ORDER BY COUNT(c.blog_id) DESC, bp.CreatedDate DESC";
        } else if ($_SESSION["sorting"] == 3) {
            return "SELECT bp.id, bp.title, bp.blogContent, u.user_name, u.profile_image, bp.CreatedDate, bp.image_base64, bp.user_id, COUNT(c.blog_id)
                    FROM blogposts bp
                    JOIN users AS u ON bp.user_id = u.id 
                    JOIN comments AS c ON c.blog_id = bp.id
                    WHERE c.CreatedDate >= NOW() - INTERVAL 1 DAY
                    GROUP BY c.blog_id
                    ORDER BY COUNT(c.blog_id) DESC, bp.CreatedDate DESC";
        }
        return ''; 
    }





}