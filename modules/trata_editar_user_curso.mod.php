<?php
include '../include/config.inc.php';
echo 'aqui';
pr($_POST);

$disciplinas = $_POST['Disciplina'];
$turmas = $_POST['Turma'];
$direcao_turma = $_POST['direcao_turma'];
$id_user = $_POST['id_user'];
$id_curso = $_SESSION['id_curso'];

$arr_disciplinas_user = buscar_disciplinas_cargo($id_user, 'professor', $_SESSION['id_curso']);
if(count($disciplinas) > 0) {
    
    $sql = "DELETE FROM rel_disciplina_user WHERE id_user = $id_user AND id_curso = $id_curso";
    my_query($sql);

    foreach($disciplinas as $id_disciplina) {

        $sql = "INSERT INTO rel_disciplina_user (id_user, id_disciplina, id_curso, cargo) VALUES ($id_user, $id_disciplina, $id_curso ,'professor')";
        my_query($sql);
    }
}
if(count($turmas) > 0) {
        
    $arr_turmas_participa_curso = buscar_turmas_participa_curso($id_user, $_SESSION['id_curso']);
    
    foreach($arr_turmas_participa_curso as $id_turma) {
        $id_turma = $id_turma['id'];
        $sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_turma";
        my_query($sql);
    }

    foreach($turmas as $id_turma) {
        
        $sql = "INSERT INTO rel_turma_user (id_user, id_turma) VALUES ($id_user, $id_turma)";
        my_query($sql);
    } 
    
}

header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');