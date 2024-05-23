<?php
include_once '../include/config.inc.php';
global $arrConfig;

$email = $_POST['email'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$confirmar_pass = $_POST['confirmar_pass'];
$cargo = $_POST['cargo'];
$pfp = 'e.png';

if(trim($email) == '' || trim($user) == '' || trim($pass) == '' || trim($confirmar_pass) == '' || trim($cargo) == '') { /* validar campos */
    $_SESSION['erro'] = 'Preencha todos os campos';
    header('Location: ' . $arrConfig['url_site'] . '/pages/auth/registo.php');
    exit;
}

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

$_SESSION['tmp_acc'] = array(
    'user' => $user,
    'email' => $email,
    'pass' => $pass,
    'cargo' => $cargo,
    'pfp' => $pfp
);

// -- enviar o email;
$codigo = rand(10000, 99999);
$_SESSION['codigo'] = $codigo;

email_verificacao($email, $user, $codigo);

header('Location: ' . $arrConfig['url_paginas'] . 'auth/verificar_email.php');





