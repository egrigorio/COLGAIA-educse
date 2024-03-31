<?php
include_once '../include/config.inc.php';
if(!isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}

$codigo = $_POST['codigo'];
var_dump($_POST);
echo $codigo;
echo $_SESSION['codigo'];
if($codigo != $_SESSION['codigo']) {
    $_SESSION['erro'] = 'Código inválido';
    header('Location: ../auth/verificar_email.php');
    exit;
} 

$sql = "UPDATE users SET ativo = 1 WHERE id = " . $_SESSION['id'];
my_query($sql);
header('Location: ' . $arrConfig['url_paginas'] . 'auth/login.php');


