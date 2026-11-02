<?php

namespace Educse\Services;

use Educse\Exceptions\HttpException;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

final class SubjectService
{
    public function __construct(private SchoolRepository $db, private SchoolPolicy $policy) {}

    public function addToInstitution(array $names): void
    {
        if ($names === []) { throw new HttpException('Adicione pelo menos uma disciplina.'); }
        $this->db->transaction(function () use ($names) {
            $institution = $this->policy->institution();
            $this->db->one('instituicao', ['id' => $institution['id']], true);
            foreach ($names as $name) {
                if (!is_string($name) || trim($name) === '' || mb_strlen($name) > 255) { throw new HttpException('Nome de disciplina inválido'); }
                $name = trim($name);
                $subject = $this->db->one('disciplinas', ['nome' => $name]);
                if (!$subject) {
                    $words = preg_split('/\s+/u', $name);
                    $abbr = count($words) > 1 ? implode('', array_map(fn ($word) => mb_substr($word, 0, 1), $words)) : mb_substr($name, 0, 3);
                    $id = $this->db->insert('disciplinas', ['nome' => $name, 'abreviatura' => mb_strtoupper(mb_substr($abbr, 0, 40))]);
                } else { $id = $subject['id']; }
                $this->db->ensure('rel_instituicao_disciplinas', ['id_instituicao' => $institution['id'], 'id_disc' => $id]);
            }
        });
    }

    public function addToCourse(int $courseId, array $subjects): void
    {
        $this->db->transaction(function () use ($courseId, $subjects) {
            $this->policy->course($courseId);
            $institution = $this->db->institutionForCourse($courseId) ?? throw new HttpException('Curso sem instituição');
            $allowed = array_map('intval', array_column($this->db->subjectsForInstitution((int) $institution['id']), 'id'));
            if (array_diff($subjects, $allowed)) { throw new HttpException('Disciplina não pertence à instituição', 403); }
            $this->db->one('curso', ['id' => $courseId], true);
            foreach ($subjects as $id) { $this->db->ensure('rel_disciplina_curso', ['id_curso' => $courseId, 'id_disciplina' => $id]); }
        });
    }

    public function removeFromCourse(int $course, int $subject): void
    {
        $this->db->transaction(function () use ($course, $subject) {
            $this->policy->course($course);
            foreach (['rel_disciplina_user', 'rel_disciplina_curso_ano', 'rel_disciplina_curso'] as $table) { $this->db->delete($table, ['id_curso' => $course, 'id_disciplina' => $subject]); }
            $classes = array_column($this->db->all('turma', ['id_curso' => $course]), 'id');
            $this->db->delete('rel_disciplina_turma', ['id_turma' => $classes, 'id_disciplina' => $subject]);
            // As atividades existentes permanecem como histórico, sem apagar a disciplina global.
        });
    }

    public function removeFromInstitution(int $subject): void
    {
        $this->db->transaction(function () use ($subject) {
            $institution = $this->policy->institution();
            if (!$this->db->one('rel_instituicao_disciplinas', ['id_instituicao' => $institution['id'], 'id_disc' => $subject])) { throw new HttpException('Disciplina não autorizada', 403); }
            $courses = array_column($this->db->all('rel_instituicao_curso', ['id_instituicao' => $institution['id']]), 'id_curso');
            if ($this->db->one('rel_disciplina_curso', ['id_curso' => $courses, 'id_disciplina' => $subject])) { throw new HttpException('Remova primeiro a disciplina dos cursos que a utilizam.'); }
            $this->db->delete('rel_instituicao_disciplinas', ['id_instituicao' => $institution['id'], 'id_disc' => $subject]);
            if (!$this->db->one('rel_instituicao_disciplinas', ['id_disc' => $subject]) && !$this->db->one('rel_disciplina_curso', ['id_disciplina' => $subject]) && !$this->db->one('atividades', ['id_disciplina' => $subject])) {
                $this->db->delete('disciplinas', ['id' => $subject]);
            }
        });
    }

    public function curriculum(int $courseId, array $fields): void
    {
        $this->db->transaction(function () use ($courseId, $fields) {
            $course = $this->policy->course($courseId);
            $this->db->one('curso', ['id' => $courseId], true);
            $allowed = array_map('intval', array_column($this->db->all('rel_disciplina_curso', ['id_curso' => $courseId]), 'id_disciplina'));
            $selected = [];
            foreach ($fields as $field) {
                $subject = (int) $field['matches'][1];
                $year = (int) $field['matches'][2];
                if (!in_array($subject, $allowed, true) || $year < 1 || $year > $course['duracao'] || $field['value'] !== 'on') { throw new HttpException('Plano curricular inválido'); }
                $selected[$subject][$year] = true;
            }
            $classes = $this->db->all('turma', ['id_curso' => $courseId]);
            $this->db->delete('rel_disciplina_curso_ano', ['id_curso' => $courseId]);
            $this->db->delete('rel_disciplina_turma', ['id_turma' => array_column($classes, 'id')]);
            foreach ($selected as $subject => $years) {
                foreach ($years as $year => $_) { $this->db->insert('rel_disciplina_curso_ano', ['id_curso' => $courseId, 'id_disciplina' => $subject, 'ano' => $year]); }
                foreach ($classes as $class) {
                    if (isset($years[$class['ano']])) { $this->db->insert('rel_disciplina_turma', ['id_turma' => $class['id'], 'id_disciplina' => $subject]); }
                }
            }
        });
    }
}
