<?php 
include '../include/config.inc.php';
pr($_POST);

$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$comeco = $_POST['comeco'];
$fim = $_POST['fim'];
$tipo = $_POST['tipo'];
$disciplina = $_POST['disciplina'];
$tempo_sugerido = $_POST['tempo_sugerido'];
$id_turma = $_POST['id_turma'];
$id_professor = $_SESSION['id'];
$id_evento = $_POST['id_evento'];

if($comeco > $fim){
    echo "Data de início não pode ser maior que a data de fim";
    exit;
}

$sql = "UPDATE eventos SET titulo = '$titulo' ,comeco = '$comeco', fim = '$fim' WHERE id = $id_evento";
my_query($sql);

$sql = "UPDATE atividades SET descricao = '$descricao', tipo = '$tipo', id_disciplina = '$disciplina', tempo_sugerido = '$tempo_sugerido' WHERE id_evento = $id_evento";
my_query($sql);

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma );
