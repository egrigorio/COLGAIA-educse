<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
Educse\Http\ReadAccess::require('course');
if (($_GET['modulo'] ?? '') !== 'turma' || ($_GET['tipo'] ?? '') !== 'inserir') {
    http_response_code(404);
    exit('Utilize os formulários específicos de gestão para este recurso.');
}
$course = (new Educse\Repositories\SchoolRepository())->one('curso', ['id' => (int) $_SESSION['id_curso']]);
include dirname(__DIR__) . '/header.php';
?>
<body>
<form method="post" action="<?= $arrConfig['url_modules'] ?>trata_inserir.mod.php">
    <?= csrf_field() ?>
    <input type="hidden" name="modulo" value="turma">
    <input type="hidden" name="id_curso" value="<?= (int) $course['id'] ?>">
    <label>Nome da turma <input name="nome_turma" maxlength="100" required></label>
    <label>Ano letivo <input name="ano_letivo" value="<?= Educse\Services\CourseService::currentYear() ?>" pattern="[0-9]{4}/[0-9]{2}" required></label>
    <label>Ano no curso <input type="number" name="ano" min="1" max="<?= (int) $course['duracao'] ?>" required></label>
    <button type="submit">Criar turma</button>
</form>
</body>
</html>
