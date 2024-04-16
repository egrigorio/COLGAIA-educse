<?php include '../include/config.inc.php';

$sql = "INSERT INTO turno (id_turma, numero, ativo) VALUES ({$_POST['id_turma']}, '{$_POST['novo_turno']}', 1)";
my_query($sql);

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_POST['id_turma'] . '&tab=turno');