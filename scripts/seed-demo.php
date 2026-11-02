<?php

declare(strict_types=1);

require dirname(__DIR__) . '/include/config.inc.php';
if (PHP_SAPI !== 'cli' || env_value('APP_ENV', 'production') !== 'local') {
    exit("Os dados demonstrativos só podem ser criados em APP_ENV=local pela linha de comandos.\n");
}
if (my_query('SELECT id FROM users LIMIT 1') !== []) {
    fwrite(STDERR, "O banco já contém utilizadores; a demonstração exige um banco sem utilizadores.\n");
    exit(1);
}
$password = bin2hex(random_bytes(8));
$users = new Educse\Repositories\UserRepository();
$connection = db_connection();
$connection->begin_transaction();
try {
    foreach (['instituicao', 'professor', 'aluno'] as $role) {
        $ids[$role] = $users->create([
            'user' => 'demo_' . $role, 'email' => $role . '@educse.test',
            'pass' => password_hash($password, PASSWORD_DEFAULT), 'cargo' => $role, 'pfp' => 'e.png',
        ]);
    }
    $institution = my_query('INSERT INTO instituicao (nome, id_dono) VALUES (?, ?)', 0, ['Escola de demonstração', $ids['instituicao']]);
    $course = my_query('INSERT INTO curso (nome_curso, abreviatura, duracao, id_diretor_curso) VALUES (?, ?, 3, ?)', 0, ['Informática de demonstração', 'DEMO', $ids['professor']]);
    my_query('INSERT INTO rel_instituicao_curso (id_instituicao, id_curso) VALUES (?, ?)', 0, [$institution, $course]);
    $subject = my_query('INSERT INTO disciplinas (nome, abreviatura) VALUES (?, ?)', 0, ['Programação', 'PRG']);
    my_query('INSERT INTO rel_instituicao_disciplinas (id_instituicao, id_disc) VALUES (?, ?)', 0, [$institution, $subject]);
    my_query('INSERT INTO rel_disciplina_curso (id_disciplina, id_curso) VALUES (?, ?)', 0, [$subject, $course]);
    $effort = my_query('INSERT INTO esforco (limite) VALUES (DEFAULT)');
    $class = my_query('INSERT INTO turma (nome_turma, id_curso, ano_letivo, ano, id_esforco, id_diretor_turma) VALUES (?, ?, ?, 1, ?, ?)', 0, ['10º DEMO', $course, get_ano_letivo(), $effort, $ids['professor']]);
    my_query('INSERT INTO rel_disciplina_curso_ano (id_disciplina, id_curso, ano) VALUES (?, ?, 1)', 0, [$subject, $course]);
    my_query('INSERT INTO rel_disciplina_turma (id_disciplina, id_turma) VALUES (?, ?)', 0, [$subject, $class]);
    foreach (['professor', 'aluno'] as $role) {
        my_query('INSERT INTO rel_user_curso (id_user, id_curso) VALUES (?, ?)', 0, [$ids[$role], $course]);
        $membership = my_query('INSERT INTO rel_turma_user (id_turma, id_user) VALUES (?, ?)', 0, [$class, $ids[$role]]);
        my_query('INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES (-1, ?)', 0, [$membership]);
    }
    my_query('INSERT INTO rel_disciplina_user (id_user, id_disciplina, id_curso, cargo) VALUES (?, ?, ?, ?)', 0, [$ids['professor'], $subject, $course, 'professor']);
    $start = (new DateTimeImmutable('next monday'))->format('Y-m-d');
    $end = (new DateTimeImmutable('next monday +4 days'))->format('Y-m-d');
    $event = my_query('INSERT INTO eventos (titulo, comeco, fim) VALUES (?, ?, ?)', 0, ['Exercício de programação', $start, $end]);
    $activity = my_query('INSERT INTO atividades (id_evento, descricao, tipo, id_professor, tempo_sugerido, id_disciplina) VALUES (?, ?, ?, ?, 2, ?)', 0, [$event, 'Criar uma página PHP com formulário.', 'Trabalho', $ids['professor'], $subject]);
    my_query('INSERT INTO rel_atividades_turma (id_atividade, id_turma) VALUES (?, ?)', 0, [$activity, $class]);
    $connection->commit();
} catch (Throwable $exception) {
    $connection->rollback();
    throw $exception;
}
echo "Demonstração criada. Utilizadores: demo_instituicao, demo_professor, demo_aluno\n";
echo "Password temporária comum: $password\n";
echo "Turma de demonstração: $class\n";
