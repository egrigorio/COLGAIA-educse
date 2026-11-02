<?php

use Educse\Exceptions\HttpException;
use Educse\Http\Input;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;
use Educse\Services\{ActivityService, CourseService, InvitationService, MembershipService, SubjectService};

function fails(callable $operation, int $status): void
{
    try { $operation(); }
    catch (HttpException $exception) { check($exception->status === $status, 'Estado HTTP inesperado: ' . $exception->getMessage()); return; }
    throw new RuntimeException('A operação deveria ter sido recusada');
}

// Todas as tabelas são temporárias e ocultam as tabelas persistentes desta conexão.
$schema = explode('CREATE VIEW', file_get_contents(dirname(__DIR__) . '/database/schema.sql'))[0];
preg_match_all('/CREATE TABLE (\w+)/', $schema, $matches);
foreach ($matches[1] as $table) { db_connection()->query('DROP TEMPORARY TABLE IF EXISTS `' . $table . '`'); }
db_connection()->multi_query(str_replace('CREATE TABLE ', 'CREATE TEMPORARY TABLE ', $schema));
do { if ($result = db_connection()->store_result()) { $result->free(); } }
while (db_connection()->more_results() && db_connection()->next_result());
$db = new SchoolRepository();
$users = [];
foreach (['owner' => 'instituicao', 'other_owner' => 'instituicao', 'director' => 'professor', 'other_director' => 'professor', 'teacher' => 'professor', 'student' => 'aluno', 'other_student' => 'aluno'] as $name => $role) {
    $users[$name] = $db->insert('users', ['username' => $name, 'email' => $name . '@educse.test', 'cargo' => $role, 'password' => password_hash('test password', PASSWORD_DEFAULT)]);
}
$owner = new SchoolPolicy($users['owner'], $db);
$director = new SchoolPolicy($users['director'], $db);
$teacher = new SchoolPolicy($users['teacher'], $db);
$student = new SchoolPolicy($users['student'], $db);
$otherOwner = new SchoolPolicy($users['other_owner'], $db);
$institution = $db->insert('instituicao', ['nome' => 'Escola A', 'id_dono' => $users['owner']]);
$otherInstitution = $db->insert('instituicao', ['nome' => 'Escola B', 'id_dono' => $users['other_owner']]);
$course = $db->insert('curso', ['nome_curso' => 'Informática', 'abreviatura' => 'INF', 'duracao' => 3, 'id_diretor_curso' => $users['director']]);
$otherCourse = $db->insert('curso', ['nome_curso' => 'Outro curso', 'abreviatura' => 'OUT', 'duracao' => 1, 'id_diretor_curso' => $users['other_director']]);
$db->insert('rel_instituicao_curso', ['id_instituicao' => $institution, 'id_curso' => $course]);
$db->insert('rel_instituicao_curso', ['id_instituicao' => $otherInstitution, 'id_curso' => $otherCourse]);
foreach (['director', 'teacher', 'student'] as $name) { $db->insert('rel_user_curso', ['id_user' => $users[$name], 'id_curso' => $course]); }
$db->insert('rel_user_curso', ['id_user' => $users['student'], 'id_curso' => $otherCourse]);
$db->insert('rel_user_curso', ['id_user' => $users['other_director'], 'id_curso' => $otherCourse]);
$subjects = new SubjectService($db, $owner);
$subjects->addToInstitution(["Programação d'Aplicações", 'Redes']);
$subject = (int) $db->one('disciplinas', ['nome' => "Programação d'Aplicações"])['id'];
$secondSubject = (int) $db->one('disciplinas', ['nome' => 'Redes'])['id'];
$db->insert('rel_instituicao_disciplinas', ['id_instituicao' => $otherInstitution, 'id_disc' => $subject]);
$courseSubjects = new SubjectService($db, $director);
$courseSubjects->addToCourse($course, [$subject, $secondSubject]);
$courseSubjects->addToCourse($course, [$subject]);
check(count($db->all('rel_disciplina_curso', ['id_curso' => $course])) === 2, 'Não duplicar disciplinas');
fails(fn () => (new CourseService($db, $otherOwner))->remove($course), 403);
fails(fn () => (new SubjectService($db, $student))->addToCourse($course, [$subject]), 403);
check($db->one('curso', ['id' => $course]) !== null, 'Curso alheio deve sobreviver');

$curriculum = [];
foreach ([1, 2, 3] as $year) { $curriculum[] = ['matches' => ['', (string) $subject, (string) $year], 'value' => 'on']; }
$courseSubjects->curriculum($course, $curriculum);
$courses = new CourseService($db, $director);
$current = CourseService::currentYear();
$courses->academicYear($course, $current, $current, false);
$classes = $db->all('turma', ['id_curso' => $course, 'ano_letivo' => $current]);
$class = (int) $classes[0]['id'];
check(count($classes) === 3, 'Criar todas as turmas');
fails(fn () => $courses->academicYear($course, $current, $current, false), 422);
check(count($db->all('turma', ['id_curso' => $course])) === 3, 'Repetição não duplica turmas');
$otherClass = (new CourseService($db, new SchoolPolicy($users['other_director'], $db)))->createClass($otherCourse, $current, 1);
$members = new MembershipService($db, $director);
$members->students($course, [$users['student'] => $class]);
$otherMembership = $db->insert('rel_turma_user', ['id_user' => $users['student'], 'id_turma' => $otherClass]);
fails(fn () => $members->students($course, [$users['student'] => $otherClass]), 403);
$members->teacher($course, $users['teacher'], [$subject], [$class]);
$members->directors($course, [$class => $users['teacher']]);
$members->addTurn($class, 1);
$turn = (int) $db->one('turno', ['numero' => 1])['id'];
$members->turns($class, [$users['student'] => [$turn]], 'aluno');
$membership = $db->one('rel_turma_user', ['id_user' => $users['student'], 'id_turma' => $class]);
check((int) $db->one('rel_turno_user', ['id_rel_turma_user' => $membership['id']])['id_turno'] === $turn, 'Atribuir turno ao aluno');
fails(fn () => $members->turns($class, [$users['student'] => [999999]], 'aluno'), 422);
check((int) $db->one('rel_turno_user', ['id_rel_turma_user' => $membership['id']])['id_turno'] === $turn, 'Falha preserva turno anterior');

$start = new DateTimeImmutable('next monday');
$values = ['id_turma' => $class, 'disciplina' => $subject, 'comeco' => $start->format('Y-m-d'), 'fim' => $start->modify('+4 days')->format('Y-m-d'), 'titulo' => "Teste d'SQL", 'descricao' => "Uma descrição com ' apóstrofo", 'tipo' => 'Trabalho', 'tempo_sugerido' => '2.5', 'turno' => (string) $turn];
$activities = new ActivityService($db, $teacher);
$activityId = $activities->save(new Input($values), false);
$activity = $db->one('atividades', ['id' => $activityId]);
$event = (int) $activity['id_evento'];
check($db->one('eventos', ['id' => $event])['titulo'] === "Teste d'SQL", 'Texto SQL deve ser tratado como dados');
fails(fn () => (new ActivityService($db, $student))->save(new Input($values), false), 403);
fails(fn () => (new ActivityService($db, $director))->remove($event), 403);
fails(fn () => $activities->save(new Input(array_replace($values, ['fim' => $start->modify('-1 day')->format('Y-m-d')])), false), 422);
$activities->save(new Input($values + ['id_evento' => $event]), true);
check(count($db->all('eventos')) === 1, 'Editar não cria eventos extra');
$activities->save(new Input(array_replace($values, ['fim' => $values['comeco']])), false);
check(count($db->all('atividades')) === 2, 'Atividades de um dia são válidas');
$members->removeTurn($class, $turn);
check((int) $db->one('atividades', ['id' => $activityId])['id_turno'] === -1, 'Remover turno atualiza atividades da turma');
check($db->one('rel_turma_user', ['id' => $otherMembership]) !== null, 'Preservar curso alheio');

// Simular falha depois de uma alteração real e confirmar rollback, incluindo serviços aninhados.
$before = $db->one('instituicao', ['id' => $institution])['nome'];
try {
    $db->transaction(function () use ($db, $institution) {
        $db->update('instituicao', ['nome' => 'Nome temporário'], ['id' => $institution]);
        $db->transaction(function () { throw new RuntimeException('Falha simulada'); });
    });
} catch (RuntimeException $exception) { check($exception->getMessage() === 'Falha simulada', 'Falha esperada'); }
check($db->one('instituicao', ['id' => $institution])['nome'] === $before, 'Rollback deve desfazer tudo');

$target = CourseService::nextYear($current);
$courses->academicYear($course, $current, $target, true);
$newFirst = $db->one('turma', ['id_curso' => $course, 'ano_letivo' => $target, 'ano' => 1]);
$newSecond = $db->one('turma', ['id_curso' => $course, 'ano_letivo' => $target, 'ano' => 2]);
check($db->one('rel_disciplina_turma', ['id_turma' => $newFirst['id'], 'id_disciplina' => $subject]) !== null, 'Novo primeiro ano recebe as suas disciplinas');
check($db->one('rel_turma_user', ['id_turma' => $newSecond['id'], 'id_user' => $users['student']]) !== null, 'Aluno avança de ano');
check((int) $db->one('rel_turma_user', ['id' => $membership['id']])['ativo'] === 0, 'Preservar inscrição antiga como inativa');
fails(fn () => $courses->academicYear($course, $current, $target, true), 422);

// Convites testados com transportador falso; nenhum email sai da máquina.
$_ENV['APP_URL'] = 'http://127.0.0.1:8000';
$sent = [];
$mailer = function (...$args) use (&$sent) { $sent[] = $args; };
$invitations = new InvitationService($db, $director, $mailer);
$invitations->issue($course, 'other_student@educse.test', 'aluno');
parse_str(parse_url($sent[0][1], PHP_URL_QUERY), $parameters);
$token = $parameters['token'];
$row = $invitations->find($token);
check($row['token_hash'] !== $token && strlen($token) === 64, 'Só guardar o hash do token');
fails(fn () => $invitations->accept($token, $users['student']), 403);
check($invitations->find($token)['id'] === $row['id'], 'Destinatário errado não consome convite');
$invitations->accept($token, $users['other_student']);
fails(fn () => $invitations->accept($token, $users['other_student']), 404);
check($db->one('rel_user_curso', ['id_curso' => $course, 'id_user' => $users['other_student']])['estado'] === '1', 'Convite cria inscrição ativa');
$invitations->issue($course, 'new_student@educse.test', 'aluno');
parse_str(parse_url($sent[1][1], PHP_URL_QUERY), $parameters);
$row = $invitations->find($parameters['token']);
$db->update('conf_convite', ['expira_em' => '2000-01-01 00:00:00'], ['id' => $row['id']]);
fails(fn () => $invitations->find($parameters['token']), 404);
fails(fn () => (new InvitationService($db, $otherOwner, $mailer))->cancel((int) $row['id']), 403);
$invitations->cancel((int) $row['id']);
check($db->one('conf_convite', ['id' => $row['id']]) === null, 'Diretor pode cancelar convite do próprio curso');

// Cancelar todas as caixas e relações de um curso sem destruir dados partilhados.
(new CourseService($db, $owner))->remove($course);
foreach (['curso' => ['id' => $course], 'turma' => ['id_curso' => $course], 'rel_user_curso' => ['id_curso' => $course], 'rel_disciplina_curso' => ['id_curso' => $course], 'rel_disciplina_user' => ['id_curso' => $course], 'conf_convite' => ['id_curso' => $course]] as $table => $criteria) {
    check($db->all($table, $criteria) === [], 'Sem resíduos em ' . $table);
}
check($db->all('atividades') === [] && $db->all('eventos') === [], 'Remover atividades e eventos órfãos do curso');
check($db->one('turma', ['id' => $otherClass]) !== null && $db->one('rel_turma_user', ['id' => $otherMembership]) !== null, 'Não tocar no outro curso');
check($db->one('disciplinas', ['id' => $subject]) !== null, 'Preservar disciplina partilhada');
check($db->one('users', ['id' => $users['student']]) !== null, 'Não apagar contas');
echo "Serviços de gestão, autorização, rollback e convites verificados.\n";

// Esforço inclusivo, sem divisão por zero quando os dias úteis são desativados.
$settings = ['dia_0' => 1, 'dia_1' => 1, 'dia_2' => 1, 'dia_3' => 1, 'dia_4' => 1];
check(Educse\Services\WorkloadService::workingDays(new DateTimeImmutable('2030-01-07'), new DateTimeImmutable('2030-01-13'), $settings) === 5, 'Contar semana útil');
check(Educse\Services\WorkloadService::dailyAverage(['comeco' => '2030-01-07', 'fim' => '2030-01-11', 'tempo_sugerido' => 10], $settings) === 2.0, 'Distribuir horas pelos dias úteis');
check(Educse\Services\WorkloadService::dailyAverage(['comeco' => '2030-01-07', 'fim' => '2030-01-07', 'tempo_sugerido' => 2], $settings) === 2.0, 'Atividade de um dia');
check(Educse\Services\WorkloadService::dailyAverage(['comeco' => '2030-01-07', 'fim' => '2030-01-11', 'tempo_sugerido' => 10], []) === 0.0, 'Sem dias úteis não divide por zero');
