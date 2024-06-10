<?php include '../include/config.inc.php';
pr($_GET);
$sql = "DELETE FROM rel_instituicao_disciplinas WHERE id_instituicao = {$_SESSION['id_instituicao']} AND id_disc = {$_GET['id_disciplina']}";
my_query($sql);
$sql = "DELETE FROM disciplinas WHERE id = {$_GET['id_disciplina']}";
my_query($sql);
$sql = "DELETE FROM rel_disciplina_user WHERE id_disciplina = {$_GET['id_disciplina']}";
my_query($sql);
$sql = "SELECT id FROM curso WHERE id_instituicao = {$_SESSION['id_instituicao']}";
$cursos = my_query($sql);
foreach($cursos as $curso) {
    $sql = "DELETE FROM rel_disciplina_curso WHERE id_disciplina = {$_GET['id_disciplina']} AND id_curso = {$curso['id']}";
    my_query($sql);
    $sql = "SELECT * FROM turma WHERE id_curso = {$curso['id']}";
    $turmas = my_query($sql);
    foreach($turmas as $turma) {
        $sql = "SELECT * FROM rel_atividades_turma WHERE id_turma = {$turma['id']}";
        $rel_atividades_turma = my_query($sql);
        foreach($rel_atividades_turma as $rel) {
            $sql = "DELETE FROM rel_atividades_turma WHERE id = {$rel['id']}";
            my_query($sql);
            $sql = "SELECT * FROM atividades WHERE id = {$rel['id_atividade']}";
            $atividade = my_query($sql);
            $sql = "DELETE FROM eventos WHERE id = {$atividade[0]['id_evento']}";
            my_query($sql);            
        }        
    }
}
die;
header('Location: ' . $arrConfig['url_admin'] . 'instituicao.php?tab=disciplinas');
