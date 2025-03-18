<?php     
require_once "./PDO.php";
require_once "../Dao/postsDAO.php";
require_once '../Entity/userEntity.php';
require_once '../config.php';
session_start(); 

//Kommentarer till inlägg
                    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_input']) && !empty($_POST['comment_input'])){
                        
                           
                        
                        if($_POST['comment_input'] != '')
                        {
                            $user = $_SESSION['user'];
                            $userid = $user->getId();
                        $comment = $_POST["comment_input"];
                      
                        $blog_id = $_POST["blog_id"];
                        $source = $_POST['source'] ?? '/Views/blogwall.php';
                        

                        if ($source == 'profile.php' && isset($_SESSION['follow_username'])) {
                            $source = 'profile.php?user_name=' . urlencode($_SESSION['follow_username']);
                        }
                        $postDao = new PostsDAO($pdo);
                        $addcomment = $postDao->addComment($comment, $userid, $blog_id);



                        // urlencode($comment);
                        header("Location: " . BASE_URL . $source);
                        exit();
                        };
                        


                    }
                    