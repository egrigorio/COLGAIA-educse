<?php include '../../include/config.inc.php';
$turno = $_GET['turno'] ? $_GET['turno'] : -1;
$turno == 'all' ? $turno = -1 : $turno;
$rand = rand(0, 999999);
/* pr($_GET); */
$eventos = '';
$eventos = calcular_esforco_turma($turno);
$eventos_json =  json_encode($eventos);
echo $eventos_json;




