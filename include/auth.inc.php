<?php 
include_once 'config.inc.php';

if(!isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}