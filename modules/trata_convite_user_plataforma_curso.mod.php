<?php
include '../include/config.inc.php';
/* esse é o arquivo que recebe o user que não tem conta criada e clicou no link de convite do curso */

$convite = $_GET['convite'];
$sql = "SELECT * FROM conf_convite WHERE id = $convite";
$res = my_query($sql);

if(count($res) > 0) {
    $email = $res[0]['email'];
    $id_curso = $res[0]['id_curso'];
    $cargo = $res[0]['cargo'];
    
    $_SESSION['email'] = $email;
    $_SESSION['id_curso'] = $id_curso;
    $_SESSION['cargo'] = $cargo;

    header('Location: ' . $arrConfig['url_paginas'] . 'auth/registo.php' );
} else {
    echo 'não foram encontrados dados para o convite em questão, podendo este ter sido retirado ou já ter sido utilizado.';
}



