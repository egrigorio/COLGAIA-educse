<?php

require_once dirname(__DIR__, 2) . '/bootstrap/management.php';
Educse\Http\ReadAccess::require('actor');
$events = (new Educse\Repositories\SchoolRepository())->all('eventos_alunos_calendario', ['id_user' => (int) $_SESSION['id']]);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($events);
