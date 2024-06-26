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
    if(isset($email) && $email != '') {
        $user = $arrResultado[0]['username'];
        $_SESSION['user'] = $user;
    } else {
        $_SESSION['user'] = $user;
    }
    my_query("UPDATE users SET ultimo_login = NOW() WHERE id = " . $arrResultado[0]['id']);
    $_SESSION['tipoLog'] = "Login do $user";
    $_SESSION['cargo'] = strtolower($arrResultado[0]['cargo']);
    $_SESSION['pfp'] = $arrResultado[0]['pfp'];
    $_SESSION['theme'] = 'mytheme';
    if($_SESSION['id_user'] == $_SESSION['id']) {
        $id_user = $_SESSION['id_user'];
        $id_curso = $_SESSION['id_curso'];
        if($_SESSION['dc']) {
            $sql = "UPDATE curso SET id_diretor_curso = $id_user WHERE id = $id_curso";
            my_query($sql);    
            $sql = "UPDATE curso SET ativo = 1 WHERE id = $id_curso";
            my_query($sql);
            $sql = "SELECT * FROM turma WHERE id_curso = $id_curso";
            $res = my_query($sql);
            if(count($res) > 0) {
                foreach($res as $turma) {
                    $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $turma['id'] . ", $id_user)";
                    $id_inserido = my_query($sql);
                    $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ( -1, $id_inserido)";
                    my_query($sql);

                }
            }
        } else {
            $sql = "UPDATE rel_user_curso SET estado = '1' WHERE id_user = $id_user AND id_curso = $id_curso";
            $res = my_query($sql);            
        }
        unset($_SESSION['id_user']);
        unset($_SESSION['email']);
        unset($_SESSION['id_curso']);
        unset($_SESSION['dc']);
        $sql = "DELETE FROM conf_convite WHERE id = " . $_SESSION['convite'];
        $res = my_query($sql);
        unset($_SESSION['convite']);      
    }
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} else {

$_SESSION['erro'] = 'Credenciais incorretas'; 
header('Location: ' . $arrConfig['url_site'] . '/pages/auth/login.php');
exit;


}