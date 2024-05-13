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
    $sql = "SELECT id FROM rel_turma_user WHERE id_user = " . $v['id_aluno'];
    $id_rel_turma_user = my_query($sql);
    if(count($id_rel_turma_user) > 0) {
        $id_rel_turma_user = $id_rel_turma_user[0]['id'];
        $sql = "DELETE FROM rel_turno_user WHERE id_rel_turma_user = $id_rel_turma_user";
        my_query($sql);
    }
    $sql = "DELETE FROM rel_turma_user WHERE id_user = " . $v['id_aluno'];
    my_query($sql);
    $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $v['id_turma'] . ", " . $v['id_aluno'] . ")";
    $id_rel_turma_user = my_query($sql);    
    $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES (-1, $id_rel_turma_user)";
    my_query($sql);
}
header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=alunos');

