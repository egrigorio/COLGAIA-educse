<?php

namespace Educse\Services;

use Educse\Exceptions\HttpException;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

final class MembershipService
{
    public function __construct(private SchoolRepository $db, private SchoolPolicy $policy) {}

    public function teacher(int $course, int $user, array $subjects, array $classes): void
    {
        $this->db->transaction(function () use ($course, $user, $subjects, $classes) {
            $this->policy->course($course);
            $this->policy->member($course, $user, 'professor');
            $allowed = array_map('intval', array_column($this->db->all('rel_disciplina_curso', ['id_curso' => $course]), 'id_disciplina'));
            if (array_diff($subjects, $allowed)) { throw new HttpException('Disciplina não pertence ao curso'); }
            $year = CourseService::currentYear();
            $yearClasses = $this->db->all('turma', ['id_curso' => $course, 'ano_letivo' => $year]);
            $allowedClasses = array_map('intval', array_column($yearClasses, 'id'));
            if (array_diff($classes, $allowedClasses)) { throw new HttpException('Turma não pertence ao curso e ano atual'); }
            $this->db->delete('rel_disciplina_user', ['id_curso' => $course, 'id_user' => $user]);
            foreach ($subjects as $subject) { $this->db->insert('rel_disciplina_user', ['id_curso' => $course, 'id_user' => $user, 'id_disciplina' => $subject, 'cargo' => 'professor']); }
            $memberships = $this->db->all('rel_turma_user', ['id_user' => $user, 'id_turma' => $allowedClasses]);
            $remove = array_filter($memberships, fn ($membership) => !in_array((int) $membership['id_turma'], $classes, true));
            $this->db->detachMemberships(array_column($remove, 'id'));
            foreach ($yearClasses as $class) {
                if ((int) $class['id_diretor_turma'] === $user && !in_array((int) $class['id'], $classes, true)) { $this->db->update('turma', ['id_diretor_turma' => -1], ['id' => $class['id']]); }
            }
            foreach ($classes as $class) {
                $membership = $this->db->ensure('rel_turma_user', ['id_user' => $user, 'id_turma' => $class], ['ativo' => 1]);
                $this->db->update('rel_turma_user', ['ativo' => 1], ['id' => $membership]);
                if (!$this->db->one('rel_turno_user', ['id_rel_turma_user' => $membership])) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]); }
            }
        });
    }

    public function remove(int $course, int $user): void
    {
        $this->db->transaction(function () use ($course, $user) {
            $data = $this->policy->course($course);
            if ($user === (int) $data['id_diretor_curso']) { throw new HttpException('Não pode remover o diretor do curso.'); }
            $this->db->detachMemberships(array_column($this->db->membershipsInCourse($course, $user), 'id'));
            $this->db->update('turma', ['id_diretor_turma' => -1], ['id_curso' => $course, 'id_diretor_turma' => $user]);
            $this->db->delete('rel_disciplina_user', ['id_curso' => $course, 'id_user' => $user]);
            $this->db->delete('rel_user_curso', ['id_curso' => $course, 'id_user' => $user]);
        });
    }

    public function students(int $course, array $assignments): void
    {
        $this->db->transaction(function () use ($course, $assignments) {
            $this->policy->course($course);
            foreach ($assignments as $user => $classId) {
                $this->policy->member($course, $user, 'aluno');
                $class = $this->db->one('turma', ['id' => $classId, 'id_curso' => $course, 'ativo' => 1]);
                if (!$class) { throw new HttpException('Turma não pertence ao curso', 403); }
                // Não apagar inscrições de outros cursos ou de anos letivos anteriores.
                $classes = array_column($this->db->all('turma', ['id_curso' => $course, 'ano_letivo' => $class['ano_letivo']]), 'id');
                $current = $this->db->all('rel_turma_user', ['id_user' => $user, 'id_turma' => $classes]);
                $old = array_filter($current, fn ($row) => (int) $row['id_turma'] !== $classId);
                $this->db->detachMemberships(array_column($old, 'id'));
                $membership = $this->db->ensure('rel_turma_user', ['id_user' => $user, 'id_turma' => $classId], ['ativo' => 1]);
                $this->db->update('rel_turma_user', ['ativo' => 1], ['id' => $membership]);
                if (!$this->db->one('rel_turno_user', ['id_rel_turma_user' => $membership])) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]); }
            }
        });
    }

    public function directors(int $course, array $assignments): void
    {
        $this->db->transaction(function () use ($course, $assignments) {
            $this->policy->course($course);
            $assigned = [];
            foreach ($assignments as $classId => $userId) {
                $class = $this->db->one('turma', ['id' => $classId, 'id_curso' => $course]);
                if (!$class) { throw new HttpException('Turma não pertence ao curso', 403); }
                if ($userId !== -1) {
                    $this->policy->member($course, $userId, 'professor');
                    $key = $class['ano_letivo'] . ':' . $userId;
                    if (isset($assigned[$key])) { throw new HttpException('O professor já dirige uma turma neste ano.'); }
                    $assigned[$key] = true;
                    $other = $this->db->one('turma', ['id_curso' => $course, 'ano_letivo' => $class['ano_letivo'], 'id_diretor_turma' => $userId]);
                    if ($other && (int) $other['id'] !== $classId && !array_key_exists((int) $other['id'], $assignments)) { throw new HttpException('O professor já dirige uma turma neste ano.'); }
                    $membership = $this->db->ensure('rel_turma_user', ['id_turma' => $classId, 'id_user' => $userId], ['ativo' => 1]);
                    $this->db->update('rel_turma_user', ['ativo' => 1], ['id' => $membership]);
                    if (!$this->db->one('rel_turno_user', ['id_rel_turma_user' => $membership])) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]); }
                }
            }
            foreach ($assignments as $class => $user) { $this->db->update('turma', ['id_diretor_turma' => $user], ['id' => $class, 'id_curso' => $course]); }
        });
    }

    public function addTurn(int $classId, int $number): void
    {
        $this->db->transaction(function () use ($classId, $number) {
            $this->policy->classroom($classId, true);
            $turn = $this->db->ensure('turno', ['numero' => $number], ['ativo' => 1]);
            $membership = $this->db->ensure('rel_turma_user', ['id_turma' => $classId, 'id_user' => $this->policy->id()], ['ativo' => 1]);
            $this->db->delete('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]);
            $this->db->ensure('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => $turn]);
        });
    }

    public function removeTurn(int $classId, int $turn): void
    {
        $this->db->transaction(function () use ($classId, $turn) {
            $this->policy->classroom($classId, true);
            $members = array_column($this->db->all('rel_turma_user', ['id_turma' => $classId]), 'id');
            $this->db->delete('rel_turno_user', ['id_rel_turma_user' => $members, 'id_turno' => $turn]);
            foreach ($members as $membership) {
                if (!$this->db->one('rel_turno_user', ['id_rel_turma_user' => $membership])) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $membership, 'id_turno' => -1]); }
            }
            $this->db->update('atividades', ['id_turno' => -1], ['id_turno' => $turn, 'id' => array_column($this->db->all('rel_atividades_turma', ['id_turma' => $classId]), 'id_atividade')]);
        });
    }

    public function turns(int $classId, array $assignments, string $role): void
    {
        $this->db->transaction(function () use ($classId, $assignments, $role) {
            $this->policy->classroom($classId, true);
            $allowed = $this->db->turnIds($classId);
            foreach ($assignments as $user => $turns) {
                $account = $this->db->one('users', ['id' => $user, 'cargo' => $role, 'ativo' => 1]);
                $member = $this->db->one('rel_turma_user', ['id_user' => $user, 'id_turma' => $classId, 'ativo' => 1]);
                if (!$account || !$member) { throw new HttpException('Utilizador não pertence à turma', 403); }
                $turns = array_values(array_unique($turns)) ?: [-1];
                if (array_diff($turns, [...$allowed, -1]) || ($role === 'aluno' && count($turns) > 1)) { throw new HttpException('Turnos inválidos'); }
                $this->db->delete('rel_turno_user', ['id_rel_turma_user' => $member['id']]);
                foreach ($turns as $turn) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $member['id'], 'id_turno' => $turn]); }
            }
        });
    }
}
