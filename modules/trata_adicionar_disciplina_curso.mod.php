<?php
include '../include/config.inc.php';

pr($_POST);
$id_curso = $_SESSION['id_curso'];

if(isset($_POST['disciplinas'])) {
    foreach($_POST['disciplinas'] as $disciplina) {
        $sql = "INSERT INTO rel_disciplina_curso (id_disciplina, id_curso) VALUES ($disciplina, $id_curso)";
        my_query($sql);
    }
}

header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');