<?php
include '../include/config.inc.php';
$id_curso = $_SESSION['id_curso'];
$id_disc = $_GET['id_disc'];
echo $id_disc;
$sql = "DELETE FROM rel_disciplina_curso WHERE id_disciplina = $id_disc AND id_curso = $id_curso";
my_query($sql);

$sql = "DELETE FROM rel_disciplina_user WHERE id_disciplina = $id_disc AND id_curso = $id_curso";
my_query($sql);

header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');