<?php
include '../include/config.inc.php';



$sql = "SELECT users.id, conf_convite.id_curso, conf_convite.cargo FROM users 
INNER JOIN conf_convite ON users.email = conf_convite.email 
WHERE conf_convite.id = " . $_GET['convite'];



$res = my_query($sql);
if(count($res) == 0) {
    $_SESSION['convite_aceite'] = true;
    header('Location:' . $arrConfig['url_admin'] . 'turma.php');
    exit;
}
pr($res);
$id_user = $res[0]['id'];
if(isset($_SESSION['id'])) {
    if($_SESSION['id'] == $id_user) {
        $id_curso = $res[0]['id_curso'];
        $cargo = $res[0]['cargo'];
        if($cargo == 'Diretor de Curso') {
            $cargo = 'Professor';
            $sql = "UPDATE curso SET id_diretor_curso = $id_user WHERE id = $id_curso";
            echo $sql;
            my_query($sql);
            $sql = "UPDATE curso SET ativo = 1 WHERE id = $id_curso";
            my_query($sql);
            $sql = "SELECT * FROM turma WHERE id_curso = $id_curso";
            $res = my_query($sql);
            foreach($res as $turma) {
                if(count($res) > 0) {
                    $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $turma['id'] . ", $id_user)";
                    $id_inserido = my_query($sql);
                    $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ( -1, $id_inserido)";
                    my_query($sql);
                }

            }
        } else {
            $dc = false;
        }


        $sql = "SELECT * FROM rel_user_curso WHERE id_user = $id_user AND id_curso = $id_curso";
        $res = my_query($sql);
        if(count($res) == 0) {
            $sql = "INSERT INTO rel_user_curso (id_user, id_curso, cargo, estado) VALUES ($id_user, $id_curso, '$cargo', '1')";
            my_query($sql);
        } else {
            $sql = "UPDATE rel_user_curso SET estado = '1' WHERE id_user = $id_user AND id_curso = $id_curso";
            $res = my_query($sql);
        }        
        $sql = "DELETE FROM conf_convite WHERE id = " . $_GET['convite'];
        $res = my_query($sql);

        $_SESSION['convite_aceite'] = true;

        header('Location:' . $arrConfig['url_admin'] . 'turma.php');
    } else {
        header('Location:' . $arrConfig['url_admin'] . 'index.php');
    }

} else {
    if(count($res) > 0) {
        $_SESSION['convite'] = $_GET['convite'];
        $_SESSION['id_user'] = $res[0]['id'];
        $_SESSION['email'] = $res[0]['email'];
        $_SESSION['id_curso'] = $res[0]['id_curso'];
        if($res[0]['cargo'] == 'Diretor de Curso') {            
            $_SESSION['dc'] = true;
        } else {
            $_SESSION['dc'] = false;            
        }
        header('Location: ' . $arrConfig['url_paginas'] . 'auth/login.php' );
    } else {
        echo 'não foram encontrados dados para o convite em questão, podendo este ter sido retirado ou já ter sido utilizado.';
    }
}