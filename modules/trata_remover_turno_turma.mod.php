<?php include '../include/config.inc.php';
$sql = "DELETE FROM turno WHERE id = {$_POST['id_turno']}";
my_query($sql);
header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_POST['id_turma'] . '&tab=turno');