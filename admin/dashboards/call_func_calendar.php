<?php

require_once dirname(__DIR__, 2) . '/include/config.inc.php';
Educse\Http\ReadAccess::require('classroom');
$turn = $_GET['turno'] ?? -1;
$turn = $turn === 'all' ? -1 : (int) $turn;
if ($turn !== -1 && !in_array($turn, (new Educse\Repositories\SchoolRepository())->turnIds((int) $_GET['id_turma']), true)) {
    http_response_code(422);
    exit('Turno inválido');
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(calcular_esforco_turma($turn));
