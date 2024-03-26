<?php
include_once '../include/config.inc.php';
global $arrConfig;

$email = $_POST['email'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$confirmar_pass = $_POST['confirmar_pass'];
$cargo = $_POST['cargo'];
$pfp = 'e.png';

if($pass != $confirmar_pass) { /* validar pass e confirmar */
    $_SESSION['erro'] = 'As passwords não coincidem';
    header('Location: ' . $arrConfig['url_site'] . '/pages/auth/registo.php');
    exit;
}

$sql = "SELECT * FROM users WHERE email = '$email' OR username = '$user'";
$arrResultado = my_query($sql);
if (count($arrResultado) > 0) { /* validar se já existe o user */
    $_SESSION['erro'] = 'Utilizador já existe';
    header('Location: ' . $arrConfig['url_site'] . '/pages/auth/registo.php');
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { /* validar email */
    $_SESSION['erro'] = 'Email inválido';
    header('Location: ' . $arrConfig['url_site'] . '/pages/auth/registo.php');
    exit;
}

$pass = password_hash($pass, PASSWORD_DEFAULT); /* encriptar pass */
$sql = "INSERT INTO users (username, email, password, pfp, cargo, ativo) VALUES ('$user', '$email', '$pass', '$pfp', '$cargo', 0)";
my_query($sql);

//pegar o id do user que foi criado agora
$sql = "SELECT last_insert_id() as id FROM users";
$arrResultado = my_query($sql);
$id = $arrResultado[0]['id'];

// -- enviar o email;
$codigo = rand(10000, 99999);
$_SESSION['codigo'] = $codigo;
$_SESSION['id'] = $id;
email_verificacao($email, $user, $codigo);

header('Location: ' . $arrConfig['url_paginas'] . 'auth/verificar_email.php');





