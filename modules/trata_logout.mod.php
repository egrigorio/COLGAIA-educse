<?php
include_once '../include/config.inc.php';
if(!isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}
$user = $_SESSION['user'];
$_SESSION['tipoLog'] = "Logout do user $user";

@session_destroy();
header('Location: ../index.php');