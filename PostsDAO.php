<?php

class PostsDao
{

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getComments($post_id)
    {

        $commentSql = "SELECT c.id, c.commentContent, c.CreatedDate, u.user_name, u.profile_image

                       

        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.blog_id = :blog_id
        ORDER BY c.CreatedDate DESC";
        $commentStmt = $this->pdo->prepare($commentSql);
        $commentStmt->bindParam(':blog_id', $post_id, PDO::PARAM_INT);
        $commentStmt->execute();
        $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
        $comments = array_reverse($comments);
        return $comments;

    }

    public function addComment($comment, $userid, $blog_id)
    {

        $stmt = $this->pdo->prepare("INSERT INTO comments(commentContent, user_id, blog_id)
    VALUES(:commentsContent, :userid, :blog_id) ");

        $stmt->bindParam(":commentsContent", $comment);
        $stmt->bindParam(":userid", $userid);
        $stmt->bindParam(":blog_id", $blog_id, PDO::PARAM_INT);

        if(!$stmt->execute());{
            return false;

        }
        


    }

    public function deleteComments($id){
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :id ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if(!$stmt->execute());{
            return false;
        }

    }

    public function getBlogPosts($sql){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getPic($id){
        $stmt = $this->pdo->prepare("SELECT image_base64 FROM blogposts where id = :id"); 
        $stmt->bindParam(":id", $id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function updatePost($id, $title, $content, $image){

   $stmt = $this->pdo->prepare("UPDATE blogposts SET title = :title, blogContent = :content, image_base64 = :image_base64  WHERE id = :id ");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $stmt->bindParam(":content", $content, PDO::PARAM_STR);
    $stmt->bindParam(":image_base64", $image, PDO::PARAM_STR);
    $stmt->execute();
    }
    // public function getlikes($post_id){
    //     $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    //     $stmt->execute([$post_id]);
    //     return $stmt->fetchColumn();
    // }

    public function searchPosts($searchPost){
        $stmt = $this->pdo->prepare("SELECT * FROM blogposts AS bp 
        JOIN users AS u ON bp.user_id = u.id 
        WHERE title LIKE :searchTitle OR blogContent LIKE :searchContent");
        
        $searchPost = "%". $searchPost . "%";
        $stmt->bindParam(":searchTitle", $searchPost, PDO::PARAM_STR);
        $stmt->bindParam(":searchContent", $searchPost, PDO::PARAM_STR);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $posts;
    }

}