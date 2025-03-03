<?php

session_start(); // Starta sessionen om den inte är aktiv

if (isset($_SESSION)) { 
    session_unset(); 
    session_destroy(); 
    setcookie(session_name(), '', time() - 3600, '/'); 

    header('Location: index.php');
    exit;
} else {
    header('Location: index.php');
    exit;
}