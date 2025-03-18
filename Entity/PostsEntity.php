<?php


class Posts{
    private $id;
    private $title; 
    private $blogContent;
    private $user_id; 
    private $createdDate; 
    private $image_base64; 
    

    public function __construct($id, $title, $blogContent, $user_id, $createdDate, $image_base64){
        $this->id = $id;
        $this->title = $title;
        $this->blogContent = $blogContent;
        $this->user_id = $user_id;
        $this->createdDate = $createdDate;
        $this->image_base64 = $image_base64;
        

    }

    public function getPostId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title; 
    }
    public function getBlogContent(){
        return $this->blogContent;

    }
    public function getUserId(){
        return $this->user_id;
    }
    public function getCreatedDate(){
        return $this->createdDate;
    } 
    public function getImageBase64(){
        return $this->image_base64;
    }

}

class Comments{
    private $id;
    private $commentContent; 
    private $user_id;
    private $blog_id;
    private $createdDate;
    public function __construct($id, $commentContent, $user_id, $blog_id, $createdDate){
        $this->id =$id;
        $this->commentContent = $commentContent;  
        $this->user_id = $user_id;
        $this->blog_id = $blog_id;
        $this->createdDate = $createdDate;
        
    }
    
    public function getCommentId(){
        return $this->id;
    }
    public function getCommentContent(){
    return $this->commentContent;
    }

    public function getCommentUserId(){
        return $this->user_id;
    }
    public function getBlogId(){
        return $this->blog_id;
    }
    public function getCreatedDate(){
    return $this->createdDate;
    }
}