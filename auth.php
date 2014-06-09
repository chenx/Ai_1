<?php

session_start(); 

$newip = $_SERVER['REMOTE_ADDR']; 

if (!isset($_SESSION['username']) ||
     empty($_SESSION['username']) || $newip!= $_SESSION['ip']) { 
    include "logout.php"; 
}

?>
