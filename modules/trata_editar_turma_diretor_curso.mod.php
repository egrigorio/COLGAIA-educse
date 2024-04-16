<?php include '../include/config.inc.php';
pr($_POST);
$id_turmas = array();
foreach($_POST as $key => $value){
    $key = str_replace('diretor_turma_', '', $key);
    $id_turmas[$key] = $value;
}

foreach($id_turmas as $key => $value){
    $sql = "UPDATE turma SET id_diretor_turma = $value WHERE id = $key";    
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=gestão%20das%20turmas');