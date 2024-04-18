<?php include '../include/config.inc.php';
pr($_POST);
$arr_ids_turmas = array();
foreach($_POST as $key => $value) {
    $newKey = explode('%', $key);
    $arr_ids_turmas[] = array(
        'id_aluno' => $newKey[1],
        'id_turma' => $value
    );
}
pr($arr_ids_turmas);
foreach($arr_ids_turmas as $k => $v) {
    $sql = "DELETE FROM rel_turma_user WHERE id_user = " . $v['id_aluno'];
    my_query($sql);
    $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $v['id_turma'] . ", " . $v['id_aluno'] . ")";
    echo $sql;
    my_query($sql);    
    $sql = "INSERT INTO rel_turno_user (id_turno, id_user, id_turma) VALUES (-1, " . $v['id_aluno'] . ", " . $v['id_turma'] . ")";
    my_query($sql);
}
header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=alunos');

