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

if($comeco > $fim){
    echo "Data de início não pode ser maior que a data de fim";
    exit;
}
$sql = "INSERT INTO eventos (titulo, comeco, fim, ativo) VALUES ('$titulo', '$comeco', '$fim', 1)";
$id_evento = my_query($sql);

$sql = "INSERT INTO atividades (id_evento, descricao, tipo, id_professor, tempo_sugerido, id_disciplina, ativo) VALUES ($id_evento, '$descricao', '$tipo', '$id_professor' , $tempo_sugerido, $disciplina, 1)";
$id_atividade = my_query($sql);

$sql = "INSERT INTO rel_atividades_turma (id_atividade, id_turma) VALUES ($id_atividade, $id_turma)";
my_query($sql);

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma );