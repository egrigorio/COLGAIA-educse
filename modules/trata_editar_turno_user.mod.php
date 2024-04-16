<?php include '../include/config.inc.php';

pr($_POST);
$new_post = array();
foreach($_POST as $key => $value){
    $key = str_replace('turno_', '', $key);
    $new_post[$key] = $value;
}

$id_turma = $new_post['id_turma'];
unset($new_post['id_turma']);

foreach($new_post as $key => $value){
    $sql = "SELECT id FROM turno WHERE numero = '$value' AND id_turma = $id_turma";
    echo $sql;
    $res = my_query($sql);
    
    $sql = "UPDATE rel_turno_user SET id_turno = " . $res[0]['id'] . " WHERE id_user = $key AND id_turma = $id_turma";
    echo $sql; 
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma . '&tab=alunos');


