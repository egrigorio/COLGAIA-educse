<?php include '../include/config.inc.php';

$sql = "SELECT * FROM turno WHERE numero = '{$_POST['novo_turno']}'";
$id_user = $_SESSION['id'];
$result = my_query($sql);
$sql = "SELECT id FROM rel_turma_user WHERE id_user = $id_user AND id_turma = {$_POST['id_turma']}";
$id_rel_turma_user = my_query($sql);
$id_rel_turma_user = $id_rel_turma_user[0]['id'];
if (count($result) > 0) {    
    $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ('{$result[0]['id']}', $id_rel_turma_user)";

    my_query($sql);    
} else {
    $sql = "INSERT INTO turno (numero, ativo) VALUES ('{$_POST['novo_turno']}', 1)";
    my_query($sql);
    $sql = "SELECT * FROM turno WHERE numero = '{$_POST['novo_turno']}'";
    $result = my_query($sql);
    $sql = "INSERT INTO rel_turno_user (id_rel_turma_user, id_turno) VALUES ($id_rel_turma_user, '{$result[0]['id']}')";
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_POST['id_turma'] . '&tab=turno');