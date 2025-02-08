<?php
// Initialize the session
session_start();
include(__DIR__ . '/../settings/database/conn.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$userId = $_SESSION['userId'];

?>