<?php
include '../include/config.inc.php';

pr($_POST);
pr($_SESSION);

$email = $_SESSION['email'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$confirmar_pass = $_POST['confirmar_pass'];
$cargo = $_SESSION['cargo'];
$id_curso = $_SESSION['id_curso'];
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
$sql = "INSERT INTO users (username, email, password, pfp, cargo, ativo) VALUES ('$user', '$email', '$pass', '$pfp', '$cargo', 1)";
$id = my_query($sql);

$sql = "SELECT * FROM rel_user_curso WHERE id_user = $id AND id_curso = $id_curso";
$res = my_query($sql);
if(count($res) == 0) {
    $sql = "INSERT INTO rel_user_curso (id_user, id_curso, cargo, estado) VALUES ($id, $id_curso, '$cargo', '1')";
    my_query($sql);
} else {
    // tratar exceção de o user já estar na turma
}

// -- enviar o email;
$codigo = rand(10000, 99999);
$_SESSION['codigo'] = $codigo;
$_SESSION['id'] = $id;
$_SESSION['cargo'] = $cargo;
$_SESSION['pfp'] = $pfp;
$_SESSION['user'] = $user;
$_SESSION['ultimo_login'] = date('d/m/Y H:i');

my_query("UPDATE users SET ultimo_login = NOW() WHERE id = " . $id);


header('Location: ' . $arrConfig['url_admin'] . 'index.php');





