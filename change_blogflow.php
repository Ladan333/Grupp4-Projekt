<?php

session_start();
require 'PDO.php'; 

if ($_SESSION['id'] == null) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['blogflow'] == 1 || $_SESSION['blogflow'] == null) {
    $_SESSION['blogflow'] = 2;
} else if ($_SESSION['blogflow'] == 2){
    $_SESSION['blogflow'] = 1;
}

header("Location: blogwall.php");
exit();

?>