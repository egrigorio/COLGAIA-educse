<?php
include '../include/config.inc.php';
echo 'aqui';

pr($_POST);

$emails = $_POST['emails'];

$cargo = $_GET['cargo'];
$id_curso = $_GET['id_curso'];

$emails = array_unique($emails);
foreach($emails as $email) {
    pr($email);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = my_query($sql);
    $id_user = $res[0]['id'];
    if(count($res) == 0) {
        // tratar exceção de o user não estar na turma

    } else {
        $sql = "SELECT * FROM rel_user_curso WHERE id_user = $id_user AND id_curso = $id_curso";
        $res = my_query($sql);
        if(count($res) == 0) {
            $sql = "INSERT INTO rel_user_curso (id_user, id_curso, cargo, status) VALUES ($id_user, $id_curso, '$cargo', 'Pendente')";
            my_query($sql);
        } else {
            // tratar exceção de o user já estar na turma
        }
    }

}

header('Location:' . $arrConfig['url_admin'] . 'curso.php?');
