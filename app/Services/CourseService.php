<?php

namespace Educse\Services;

use Educse\Exceptions\HttpException;
use Educse\Http\Input;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

final class CourseService
{
    public function __construct(private SchoolRepository $db, private SchoolPolicy $policy) {}

    public function institution(Input $input, bool $editing): int
    {
        return $this->db->transaction(function () use ($input, $editing) {
            $name = $input->string('nome');
            if ($editing) {
                $institution = $this->policy->institution($input->int('id_instituicao'));
                $this->db->update('instituicao', ['nome' => $name], ['id' => $institution['id']]);
                return (int) $institution['id'];
            }
            if ($this->policy->actor()['cargo'] !== 'instituicao') { throw new HttpException('Perfil não autorizado', 403); }
            // A conta é bloqueada para serializar dois pedidos de criação simultâneos.
            $this->db->one('users', ['id' => $this->policy->id()], true);
            if ($this->db->one('instituicao', ['id_dono' => $this->policy->id()], true)) { throw new HttpException('Já possui uma instituição.'); }
            return $this->db->insert('instituicao', ['nome' => $name, 'id_dono' => $this->policy->id()]);
        });
    }

    /** Persiste o curso e devolve os dados para o convite ser enviado após o commit. */
    public function save(Input $input, bool $editing): array
    {
        return $this->db->transaction(function () use ($input, $editing) {
            $institution = $this->policy->institution();
            $values = ['nome_curso' => $input->string('nome_curso'), 'abreviatura' => $input->string('abreviatura', 40), 'duracao' => $input->int('duracao', 1, 12)];
            $email = $input->string('diretor_curso', 254);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { throw new HttpException('Email do diretor inválido.'); }
            $director = $this->db->one('users', ['email' => $email]);
            if ($director && ($director['cargo'] !== 'professor' || !$director['ativo'])) { throw new HttpException('O diretor deve ser um professor ativo.'); }
            $course = $editing ? $this->policy->course($input->int('id_curso'), true) : null;
            foreach ($this->db->all('rel_instituicao_curso', ['id_instituicao' => $institution['id']]) as $relation) {
                $duplicate = $this->db->one('curso', ['id' => $relation['id_curso'], 'nome_curso' => $values['nome_curso']]);
                if ($duplicate && (int) $duplicate['id'] !== (int) ($course['id'] ?? 0)) { throw new HttpException('Curso já existe nesta instituição.'); }
            }
            if ($director) {
                $other = $this->db->one('curso', ['id_diretor_curso' => $director['id']]);
                if ($other && (int) $other['id'] !== (int) ($course['id'] ?? 0)) { throw new HttpException('Professor já dirige outro curso.'); }
            }
            $sameDirector = $course && $director && (int) $course['id_diretor_curso'] === (int) $director['id'];
            if ($sameDirector) {
                $this->db->update('curso', $values, ['id' => $course['id']]);
                return ['id' => (int) $course['id']];
            }
            $values += ['id_diretor_curso' => -1, 'ativo' => 0];
            if ($course) {
                $id = (int) $course['id'];
                $this->db->update('curso', $values, ['id' => $id]);
                $this->db->delete('conf_convite', ['id_curso' => $id, 'cargo' => 'Diretor de Curso']);
            } else {
                $id = $this->db->insert('curso', $values);
                $this->db->insert('rel_instituicao_curso', ['id_curso' => $id, 'id_instituicao' => $institution['id']]);
            }
            return ['id' => $id, 'email' => $email, 'cargo' => 'Diretor de Curso', 'nome' => $values['nome_curso']];
        });
    }

    public function remove(int $id): void
    {
        $this->db->transaction(function () use ($id) {
            $this->policy->institution();
            $this->policy->course($id, true);
            $this->db->one('curso', ['id' => $id], true);
            $classes = $this->db->all('turma', ['id_curso' => $id]);
            $classIds = array_column($classes, 'id');
            $efforts = array_column($classes, 'id_esforco');
            $activityIds = array_unique(array_column($this->db->all('rel_atividades_turma', ['id_turma' => $classIds]), 'id_atividade'));
            $this->db->detachMemberships(array_column($this->db->membershipsInCourse($id), 'id'));
            $this->db->delete('rel_disciplina_turma', ['id_turma' => $classIds]);
            $this->db->delete('rel_atividades_turma', ['id_turma' => $classIds]);
            foreach ($activityIds as $activityId) {
                if (!$this->db->one('rel_atividades_turma', ['id_atividade' => $activityId])) {
                    $activity = $this->db->one('atividades', ['id' => $activityId]);
                    $this->db->delete('atividades', ['id' => $activityId]);
                    if ($activity && !$this->db->one('atividades', ['id_evento' => $activity['id_evento']])) { $this->db->delete('eventos', ['id' => $activity['id_evento']]); }
                }
            }
            $this->db->delete('turma', ['id_curso' => $id]);
            foreach ($efforts as $effort) {
                if (!$this->db->one('turma', ['id_esforco' => $effort])) { $this->db->delete('esforco', ['id' => $effort]); }
            }
            foreach (['rel_disciplina_user', 'rel_disciplina_curso_ano', 'rel_disciplina_curso', 'rel_user_curso', 'conf_convite', 'rel_instituicao_curso'] as $table) { $this->db->delete($table, ['id_curso' => $id]); }
            $this->db->delete('curso', ['id' => $id]);
        });
    }

    public function createClass(int $courseId, string $year, int $grade, ?string $name = null): int
    {
        return $this->db->transaction(function () use ($courseId, $year, $grade, $name) {
            $course = $this->policy->course($courseId);
            if ($grade < 1 || $grade > $course['duracao']) { throw new HttpException('Ano do curso inválido'); }
            if (!preg_match('~^(\d{4})/(\d{2})$~', $year, $m) || (int) $m[2] !== ((int) $m[1] + 1) % 100) { throw new HttpException('Ano letivo inválido'); }
            if ($this->db->one('turma', ['id_curso' => $courseId, 'ano_letivo' => $year, 'ano' => $grade])) { throw new HttpException('A turma deste ano já existe.'); }
            $effort = $this->db->insert('esforco', ['limite' => 4]);
            $id = $this->db->insert('turma', ['nome_turma' => $name ?: ($grade + 9) . 'º ' . $course['abreviatura'], 'id_curso' => $courseId, 'ano_letivo' => $year, 'ano' => $grade, 'id_esforco' => $effort]);
            foreach ($this->db->all('rel_disciplina_curso_ano', ['id_curso' => $courseId, 'ano' => $grade]) as $subject) { $this->db->insert('rel_disciplina_turma', ['id_turma' => $id, 'id_disciplina' => $subject['id_disciplina']]); }
            $membership = $this->db->insert('rel_turma_user', ['id_turma' => $id, 'id_user' => $this->policy->id()]);
            $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]);
            return $id;
        });
    }

    public function academicYear(int $courseId, string $source, string $target, bool $advance): void
    {
        $this->db->transaction(function () use ($courseId, $source, $target, $advance) {
            $course = $this->policy->course($courseId);
            $this->db->one('curso', ['id' => $courseId], true);
            if ($this->db->one('turma', ['id_curso' => $courseId, 'ano_letivo' => $target], true)) { throw new HttpException('O ano letivo de destino já foi criado.'); }
            if ($advance && $target !== self::nextYear($source)) { throw new HttpException('Ano de destino inválido'); }
            $old = $this->db->all('turma', ['id_curso' => $courseId, 'ano_letivo' => $source]);
            $grades = array_map('intval', array_column($old, 'ano'));
            sort($grades);
            if ($advance && $grades !== range(1, (int) $course['duracao'])) { throw new HttpException('O ano atual não contém todas as turmas do curso.'); }
            $new = [];
            for ($grade = 1; $grade <= $course['duracao']; $grade++) { $new[$grade] = $this->createClass($courseId, $target, $grade); }
            if (!$advance) { return; }
            $oldMemberships = $this->db->all('rel_turma_user', ['id_turma' => array_column($old, 'id'), 'ativo' => 1]);
            $turns = $this->db->all('rel_turno_user', ['id_rel_turma_user' => array_column($oldMemberships, 'id')]);
            $users = $this->db->all('users', ['id' => array_values(array_unique(array_column($oldMemberships, 'id_user')))]);
            $roles = array_column($users, 'cargo', 'id');
            $grades = array_column($old, 'ano', 'id');
            $turnMap = [];
            foreach ($turns as $turn) { $turnMap[$turn['id_rel_turma_user']][] = $turn['id_turno']; }
            foreach ($oldMemberships as $membership) {
                if (($roles[$membership['id_user']] ?? '') !== 'aluno') { continue; }
                $grade = (int) $grades[$membership['id_turma']] + 1;
                if (isset($new[$grade])) {
                    $newMembership = $this->db->ensure('rel_turma_user', ['id_turma' => $new[$grade], 'id_user' => $membership['id_user']], ['ativo' => 1]);
                    foreach ($turnMap[$membership['id']] ?? [-1] as $turn) { $this->db->ensure('rel_turno_user', ['id_rel_turma_user' => $newMembership, 'id_turno' => $turn]); }
                }
            }
            $this->db->update('rel_turma_user', ['ativo' => 0], ['id' => array_column($oldMemberships, 'id')]);
        });
    }

    public static function currentYear(): string
    {
        $year = (int) date('Y') - ((int) date('n') < 9 ? 1 : 0);
        return $year . '/' . substr((string) ($year + 1), -2);
    }
    public static function nextYear(string $year): string
    {
        $start = (int) substr($year, 0, 4) + 1;
        return $start . '/' . substr((string) ($start + 1), -2);
    }
}
