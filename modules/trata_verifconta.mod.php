<?php
include_once '../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}

pr($_SESSION);
$codigo = $_POST['codigo'];
var_dump($_POST);
echo $codigo;
echo $_SESSION['codigo'];
if($codigo != $_SESSION['codigo']) {
    $_SESSION['erro'] = 'Código inválido';
    header('Location: ../auth/verificar_email.php');
    exit;

} 


$user = $_SESSION['tmp_acc']['user'];
$email = $_SESSION['tmp_acc']['email'];
$pass = $_SESSION['tmp_acc']['pass'];
$cargo = $_SESSION['tmp_acc']['cargo'];
$pfp = $_SESSION['tmp_acc']['pfp'];

$sql = "INSERT INTO users (username, email, password, cargo, pfp, ativo) VALUES ('$user', '$email', '$pass', '$cargo', '$pfp', 1)";
my_query($sql);

unset($_SESSION['tmp_acc']);
header('Location: ' . $arrConfig['url_paginas'] . 'auth/login.php');


