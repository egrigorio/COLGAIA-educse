<?php include '../include/config.inc.php'; 
$id_evento = $_GET['id_evento'];
$id_user = $_SESSION['id'];

$sql = "SELECT id FROM atividades WHERE id_evento = $id_evento AND id_professor = $id_user";
$res = my_query($sql);
if(count($res) > 0) {
    $id_atividade = $res[0]['id'];
    $sql = "DELETE FROM atividades WHERE id = $id_atividade";
    my_query($sql);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

