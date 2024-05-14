<?php
include '../include/config.inc.php';
echo 'aqui';
pr($_POST);

$disciplinas = isset($_POST['Disciplina']) ? $_POST['Disciplina'] : array();
$turmas = isset($_POST['Turma']) ? $_POST['Turma'] : array();
pr($disciplinas);
pr($turmas);

$id_user = isset($_POST['id_user']) ? $_POST['id_user'] : '';
$id_curso = $_SESSION['id_curso'] ? $_SESSION['id_curso'] : '';

$arr_disciplinas_user = buscar_disciplinas_cargo($id_user, 'professor', $_SESSION['id_curso']);

if(count($disciplinas) > 0) {
    
    $sql = "DELETE FROM rel_disciplina_user WHERE id_user = $id_user AND id_curso = $id_curso";
    my_query($sql);

    foreach($disciplinas as $id_disciplina) {

        $sql = "INSERT INTO rel_disciplina_user (id_user, id_disciplina, id_curso, cargo) VALUES ($id_user, $id_disciplina, $id_curso ,'professor')";
        echo $sql;
        my_query($sql);
    }
} else {
    $sql = "DELETE FROM rel_disciplina_user WHERE id_user = $id_user AND id_curso = $id_curso";
    my_query($sql);

}
if(count($turmas) > 0) {
    $flag_direcao = false;
    $id_curso = $_SESSION['id_curso'];
    $sql = "SELECT * FROM turma WHERE id_diretor_turma = $id_user AND id_curso = $id_curso";
    $arr_turma = my_query($sql);
    $id_direcao = (count($arr_turma) > 0 ? $arr_turma[0]['id'] : -1);
    foreach($turmas as $id_turma) {
        if($id_turma == $id_direcao) {
            $flag_direcao = true;
        }
    }
    if(!$flag_direcao) { 
        $sql = "UPDATE turma SET id_diretor_turma = -1 WHERE id = $id_direcao AND id_curso = $id_curso";
        my_query($sql);
        $sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_direcao";
        my_query($sql);
    }

    $sql = "SELECT * FROM rel_turma_user  
            INNER JOIN turma ON rel_turma_user.id_turma = turma.id
            WHERE turma.id_curso = $id_curso AND id_user = $id_user";
    $arr_turmas_participa_curso = my_query($sql);
    foreach($arr_turmas_participa_curso as $id_turma) {
        $id_turma = $id_turma['id'];
        $sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_turma";
        my_query($sql);
    }            

    foreach($turmas as $id_turma) {                        
        $sql = "INSERT INTO rel_turma_user (id_user, id_turma) VALUES ($id_user, $id_turma)";
        echo $sql;
        $id_rel_turma_user =my_query($sql);
        $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES (-1, $id_rel_turma_user)";
        echo $sql;
        my_query($sql);
    } 
    $sql = "SELECT * FROM rel_turma_user WHERE id_user = $id_user";
    $arr_turmas = my_query($sql);
    
} else {
    $arr_turmas_participa_curso = buscar_turmas_participa_curso($id_user, $_SESSION['id_curso']);
    $sql = "SELECT * FROM turma WHERE id_diretor_turma = $id_user";
    $arr_turma = my_query($sql);
    $id_direcao = (count($arr_turma) > 0 ? $arr_turma[0]['id'] : -1);
    if($id_direcao != -1) {
        $sql = "UPDATE turma SET id_diretor_turma = -1 WHERE id = $id_direcao";
        my_query($sql);
        $sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_direcao";
        my_query($sql);
    }
    foreach($arr_turmas_participa_curso as $id_turma) {
        $id_turma = $id_turma['id'];
        $sql = "DELETE FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_turma";
        my_query($sql);
    }

}

header ('Location: ' . $arrConfig['url_admin'] . 'curso.php');