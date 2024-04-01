<?php
include '../include/config.inc.php';
$id_user = $_GET['id_user'];

$sql = "DELETE FROM rel_user_curso WHERE id_user = $id_user AND id_curso = {$_SESSION['id_curso']}";
my_query($sql);


$sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma IN (SELECT id FROM turma WHERE id_curso = {$_SESSION['id_curso']})";
my_query($sql);

$sql = "SELECT id_diretor_turma FROM turma WHERE id_diretor_turma = $id_user AND id_curso = {$_SESSION['id_curso']}";
$res = my_query($sql);
if(count($res) > 0) {
    $sql = "UPDATE turma SET id_diretor_turma = -1 WHERE id_diretor_turma = $id_user AND id_curso = {$_SESSION['id_curso']}";
    my_query($sql);
}

$sql = "DELETE FROM rel_disciplina_user WHERE id_user = $id_user AND id_curso = {$_SESSION['id_curso']}";
my_query($sql);


header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');