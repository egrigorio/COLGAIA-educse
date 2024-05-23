<?php
include '../include/config.inc.php';
pr($_POST);
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$comeco = $_POST['comeco'];
$fim = $_POST['fim'];
$tipo = $_POST['tipo'];
$disciplina = $_POST['disciplina'];
$turno = $_POST['turno'] ? $_POST['turno'] : 'NULL';
$turno == 'all' ? $turno = -1 : $turno = $turno;
$tempo_sugerido = $_POST['tempo_sugerido'];
$id_turma = $_POST['id_turma'];
$id_professor = $_SESSION['id'];

if($comeco > $fim){
    $_SESSION['erro'] = "Data de início não pode ser maior que a data de fim";
    header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
    exit;
} else if($comeco == $fim) {
    $_SESSION['erro'] = "Data de início não pode ser igual a data de fim";
    header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
    exit;
} else if($comeco < date('Y-m-d')) {
    $_SESSION['erro'] = "Data de início não pode ser menor que a data atual";
    header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
    exit;
} else if($fim < date('Y-m-d')) {
    $_SESSION['erro'] = "Data de fim não pode ser menor que a data atual";
    header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
    exit;
}
$sql = "INSERT INTO eventos (titulo, comeco, fim, ativo) VALUES ('$titulo', '$comeco', '$fim', 1)";
$id_evento = my_query($sql);

$sql = "INSERT INTO atividades (id_evento, descricao, tipo, id_professor, tempo_sugerido, id_disciplina, id_turno, ativo) VALUES ($id_evento, '$descricao', '$tipo', '$id_professor' , $tempo_sugerido, $disciplina, $turno, 1)";
$id_atividade = my_query($sql);

$sql = "INSERT INTO rel_atividades_turma (id_atividade, id_turma) VALUES ($id_atividade, $id_turma)";
my_query($sql);

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma );