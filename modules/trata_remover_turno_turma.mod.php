<?php include '../include/config.inc.php';
$id_turno = $_POST['id_turno'];
$id_turma = $_POST['id_turma'];

/* $sql = "SELECT * FROM view_turnos_turma WHERE id_turma = $id_turma AND id_turno = $id_turno"; */


$sql = "SELECT id FROM rel_turma_user WHERE id_turma = $id_turma AND ativo = 1";
$res = my_query($sql);
pr($res);

foreach($res as $k => $v) {
    $sql = "UPDATE rel_turno_user 
            SET id_turno = -1 
            WHERE id_turno = $id_turno AND id_rel_turma_user = {$v['id']}";
    echo $sql;    
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_POST['id_turma'] . '&tab=turno');