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

    // public function getlikes($post_id){
    //     $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    //     $stmt->execute([$post_id]);
    //     return $stmt->fetchColumn();
    // }

}