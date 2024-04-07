<?php
include '../include/config.inc.php';
if(filter_var($_POST['user_or_mail'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['user_or_mail'];    
    $sql = "SELECT * FROM users WHERE email = '$email'";
} else {
    $user = $_POST['user_or_mail'];
    $sql = "SELECT * FROM users WHERE username = '$user'";
}

$arrResultado = my_query($sql);

if(count($arrResultado) == 0) {
    $_SESSION['erro'] = 'Utilizador não encontrado';
    header('Location: ' . $arrConfig['url_site'] . '/pages/auth/login.php');
    exit;
} else if(password_verify($_POST['password'], $arrResultado[0]['password'])) {
    if($arrResultado[0]['ativo'] == 0) {
        $_SESSION['erro'] = 'Conta não verificada';
        exit;
    }
    $_SESSION['ultimo_login'] = $arrResultado[0]['ultimo_login'];
    $_SESSION['id'] = $arrResultado[0]['id'];
    if($email != '') {
        $_SESSION['user'] = $email;
    } else {
        $_SESSION['user'] = $user;
    }
    my_query("UPDATE users SET ultimo_login = NOW() WHERE id = " . $arrResultado[0]['id']);
    $_SESSION['tipoLog'] = "Login do $user";
    $_SESSION['cargo'] = $arrResultado[0]['cargo'];
    $_SESSION['pfp'] = $arrResultado[0]['pfp'];
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} else {

$_SESSION['erro'] = 'Credenciais incorretas'; 
header('Location: ' . $arrConfig['url_site'] . '/pages/auth/login.php');
exit;


}