<?php include '../include/config.inc.php';

$sql = "DELETE FROM rel_instituicao_curso WHERE id_curso = {$_GET['id_curso']} AND id_instituicao = {$_SESSION['id_instituicao']}";
my_query($sql);

$sql = "DELETE FROM curso WHERE id = {$_GET['id_curso']}";
echo $sql;

my_query($sql);

$sql = "DELETE FROM rel_user_curso WHERE id_curso = {$_GET['id_curso']}";
my_query($sql);

$sql = "DELETE FROM rel_disciplina_curso_ano WHERE id_curso = {$_GET['id_curso']}";
my_query($sql);

$sql = "DELETE FROM rel_disciplina_curso WHERE id_curso = {$_GET['id_curso']}";
my_query($sql);

$sql = "SELECT * FROM turma WHERE id_curso = {$_GET['id_curso']}";
$arrTurmas = my_query($sql);
if(count($arrTurmas) > 0) {
    foreach($arrTurmas as $turma) {
        $sql = "SELECT * FROM rel_turma_user WHERE id_turma = {$turma['id']}";
        $arrRelTurmaUser = my_query($sql);
        if (count($arrRelTurmaUser) > 0) {
            foreach($arrRelTurmaUser as $rel) {
                $sql = "DELETE FROM rel_turno_user WHERE id_rel_turma_user = {$rel['id']}";
                my_query($sql);
            }
        }
        $sql = "DELETE FROM rel_turma_user WHERE id_turma = {$turma['id']}";
        my_query($sql);
        $sql = "DELETE FROM rel_disciplina_turma WHERE id_turma = {$turma['id']}";
        my_query($sql);
        $sql = "DELETE FROM rel_atividades_turma WHERE id_turma = {$turma['id']}";
        my_query($sql);     
    }
}
$redirect = $_SERVER['HTTP_REFERER'];
$redirect = explode('?', $redirect);
$redirect = $redirect[0];

header('Location: ' . $redirect . '?tab=cursos');