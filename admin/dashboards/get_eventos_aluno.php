<?php
include '../../include/config.inc.php';
$sql = "SELECT * FROM eventos_alunos_calendario WHERE id_user = {$_SESSION['id']}";
$res = my_query($sql);
$eventos = [];
foreach($res as $evento) {
    $eventos[] = [
        'id' => $evento['id'],
        'start' => $evento['start'],
        'end' => $evento['end'],
        'title' => $evento['title'],
        'description' => $evento['description'],
        'backgroundColor' => $evento['backgroundColor']
    ];
}
echo json_encode($eventos);
