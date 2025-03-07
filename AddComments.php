<?php     
require "PDO.php";
session_start();
//Kommentarer till inlägg
                    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_input']) && !empty($_POST['comment_input'])){
                        if($_POST['comment_input'] != '')
                        {
                        $comment = $_POST["comment_input"];
                        $userid = $_SESSION["id"];
                        $blog_id = $_POST["blog_id"];
                        $source = $_POST['source'] ?? 'blogwall.php';

                        $stmt = $pdo->prepare( "INSERT INTO comments(commentContent, user_id, blog_id)
                                                       VALUES(:commentsContent, :userid, :blog_id) ");
                        
                        $stmt->bindParam(":commentsContent" , $comment);
                        $stmt->bindParam(":userid", $userid) ;
                        $stmt->bindParam(":blog_id", $blog_id, PDO::PARAM_INT);

                        $stmt->execute();

                        // urlencode($comment);
                        header("Location: $source");
                        exit();
                        };
                        


                    }
                    