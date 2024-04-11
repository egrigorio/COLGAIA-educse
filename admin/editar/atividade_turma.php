<?php

include_once '../../include/config.inc.php';
echo $arrConfig['dir_admin'] . 'dashboards/layout.dash.php';

$id_evento = $_GET['id_evento'];
$sql = "SELECT eventos.*, atividades.* FROM eventos 
INNER JOIN atividades ON eventos.id = atividades.id_evento AND atividades.id_professor = {$_SESSION['id']} 
WHERE eventos.id = $id_evento";
$res = my_query($sql);

criar_atividade_turma('3');

