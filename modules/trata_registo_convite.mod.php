<?php
include '../include/config.inc.php';

pr($_POST);
pr($_SESSION);

$email = $_SESSION['email'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$confirmar_pass = $_POST['confirmar_pass'];
$_SESSION['cargo'] = strtolower($_SESSION['cargo']);
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
    
    if($_SESSION['dc']) {
        $sql = "UPDATE curso SET id_diretor_curso = $id WHERE id = $id_curso";
        my_query($sql);    
        $sql = "UPDATE curso SET ativo = 1 WHERE id = $id_curso";
        my_query($sql);
        $sql = "SELECT * FROM turma WHERE id_curso = $id_curso";
        $res = my_query($sql);
        if(count($res) > 0) {
            foreach($res as $r) {
                $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $r['id'] . ", $id)";
                $id_inserido = my_query($sql);
                $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ( -1, $id_inserido)";
                my_query($sql);

            }
        }
        $sql = "INSERT INTO rel_user_curso (id_user, id_curso, estado) VALUES ($id, $id_curso, '1')";
        my_query($sql);
    } else {
        $sql = "INSERT INTO rel_user_curso (id_user, id_curso, estado) VALUES ($id, $id_curso, '1')";
        echo $sql;
        my_query($sql);
    }
    unset($_SESSION['id_curso']);
    unset($_SESSION['email']);
    unset($_SESSION['dc']);
    unset($_SESSION['id_user']);
    $_SESSION['convite_aceite'] = 1;
    $sql = "DELETE FROM conf_convite WHERE id = " . $_SESSION['convite'];
    $res = my_query($sql);
    unset($_SESSION['convite']);
} else {
    // tratar exceção de o user já estar na turma
}

// -- enviar o email;
$codigo = rand(10000, 99999);
$_SESSION['codigo'] = $codigo;
$_SESSION['id'] = $id;
$_SESSION['cargo'] = strtolower($cargo);
$_SESSION['theme'] = 'mytheme';
$_SESSION['pfp'] = $pfp;
$_SESSION['user'] = $user;
$_SESSION['ultimo_login'] = date('d/m/Y H:i');

my_query("UPDATE users SET ultimo_login = NOW() WHERE id = " . $id);


header('Location: ' . $arrConfig['url_admin'] . 'index.php');





