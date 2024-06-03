<?php
include '../include/config.inc.php';

pr($_POST);
$id_user = $_SESSION['id'];
$eventos = json_decode($_POST['eventos'], true);

foreach($eventos as $evento) {
    $sql = "INSERT INTO eventos_alunos_calendario (start, end, title, description, backgroundColor, id_user) VALUES ('{$evento['start']}', '{$evento['end']}', '{$evento['title']}', '{$evento['description']}', '{$evento['backgroundColor']}', $id_user)";
    $res = my_query($sql);
    if($res) {
        echo 'Evento adicionado com sucesso';
    } else {
        echo 'Erro ao adicionar evento';
    }    
}