<?php
class User
{
    private $id;
    private $first_name;
    private $last_name;
    private $user_name;
    private $pwd;
    private $email;
    private $role;
    private $profileContent;
    private $profile_image;
    private $createdDate;

    // Constructor with all necessary parameters
    public function __construct($id, $first_name, $last_name, $user_name, $pwd, $email, $role, $profileContent, $profile_image, $createdDate)
    {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->user_name = $user_name;
        $this->pwd = $pwd;
        $this->email = $email;
        $this->role = $role;
        $this->profileContent = $profileContent;
        $this->profile_image = $profile_image;
        $this->createdDate = $createdDate;
    }

    // Getter methods for all fields
    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function getPassword()
    {
        return $this->pwd;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getProfileContent()
    {
        return $this->profileContent;
    }

    public function getProfileImage()
    {
        return $this->profile_image;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }
}

