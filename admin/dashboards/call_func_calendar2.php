<?php include '../../include/config.inc.php';
//include_once $arrConfig['dir_site'] . '/header.php';
/* include_once $arrConfig['dir_admin'] . 'dashboards/gerar_calendario_atividades.php'; */
$turno = $_GET['turno'] ? $_GET['turno'] : -1;
$turno == 'all' ? $turno = -1 : $turno;
$rand = rand(0, 999999);
/* pr($_GET); */
echo gerar_calendario_atividades($rand, $turno);

