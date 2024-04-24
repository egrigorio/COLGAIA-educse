<?php include '../include/config.inc.php';

$sql = "SELECT * FROM turno WHERE numero = '{$_POST['novo_turno']}'";
$result = my_query($sql);
if (count($result) > 0) {
    $sql = "INSERT INTO rel_turno_user (id_turma, id_turno) VALUES ('{$_POST['id_turma']}', '{$result[0]['id']}')";
    my_query($sql);    
} else {
    $sql = "INSERT INTO turno (numero, ativo) VALUES ('{$_POST['novo_turno']}', 1)";
    my_query($sql);
    $sql = "SELECT * FROM turno WHERE numero = '{$_POST['novo_turno']}'";
    $result = my_query($sql);
    $sql = "INSERT INTO rel_turno_user (id_turma, id_turno) VALUES ('{$_POST['id_turma']}', '{$result[0]['id']}')";
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_POST['id_turma'] . '&tab=turno');