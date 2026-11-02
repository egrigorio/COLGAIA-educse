<?php

include_once __DIR__ . '/../../include/config.inc.php';
Educse\Http\ReadAccess::require('classroom');

$id_evento = $_GET['id_evento'];
$sql = "SELECT eventos.*, atividades.* FROM eventos 
INNER JOIN atividades ON eventos.id = atividades.id_evento AND atividades.id_professor = {$_SESSION['id']} 
WHERE eventos.id = $id_evento";
$res = my_query($sql);

criar_atividade_turma('3');

