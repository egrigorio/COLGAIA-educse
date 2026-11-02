<?php

namespace Educse\Policies;

use Educse\Exceptions\HttpException;
use Educse\Repositories\SchoolRepository;

final class SchoolPolicy
{
    private ?array $actor = null;
    public function __construct(private int $userId, private SchoolRepository $db = new SchoolRepository()) {}
    public function id(): int { return $this->userId; }

    public function actor(): array
    {
        if ($this->userId < 1) { throw new HttpException('Inicie sessão para continuar', 401); }
        $this->actor ??= $this->db->one('users', ['id' => $this->userId, 'ativo' => 1]);
        return $this->actor ?? throw new HttpException('Sessão inválida', 401);
    }

    public function institution(?int $id = null): array
    {
        if ($this->actor()['cargo'] !== 'instituicao') { $this->deny(); }
        $criteria = ['id_dono' => $this->userId, 'ativo' => 1];
        if ($id !== null) { $criteria['id'] = $id; }
        return $this->db->one('instituicao', $criteria, true) ?? throw new HttpException('Instituição não autorizada', 403);
    }

    public function course(int $id, bool $allowInstitution = false): array
    {
        $actor = $this->actor();
        $course = $this->db->one('curso', ['id' => $id], true) ?? throw new HttpException('Curso não encontrado', 404);
        if ($actor['cargo'] === 'professor' && (int) $course['id_diretor_curso'] === $this->userId) { return $course; }
        if ($allowInstitution && $actor['cargo'] === 'instituicao') {
            $institution = $this->institution();
            if ($this->db->one('rel_instituicao_curso', ['id_curso' => $id, 'id_instituicao' => $institution['id']])) { return $course; }
        }
        $this->deny();
    }

    public function classroom(int $id, bool $manage = false): array
    {
        $actor = $this->actor();
        $class = $this->db->one('turma', ['id' => $id, 'ativo' => 1]) ?? throw new HttpException('Turma não encontrada', 404);
        $course = $this->db->one('curso', ['id' => $class['id_curso']]);
        if ($actor['cargo'] === 'professor' && ((int) $class['id_diretor_turma'] === $this->userId || (int) ($course['id_diretor_curso'] ?? -1) === $this->userId)) { return $class; }
        if (!$manage && $this->db->one('rel_turma_user', ['id_turma' => $id, 'id_user' => $this->userId, 'ativo' => 1])) { return $class; }
        $this->deny();
    }

    public function teach(int $classId, int $subjectId): array
    {
        $class = $this->classroom($classId);
        if ($this->actor()['cargo'] !== 'professor' || !$this->db->one('rel_disciplina_turma', ['id_turma' => $classId, 'id_disciplina' => $subjectId])) { $this->deny(); }
        $course = $this->db->one('curso', ['id' => $class['id_curso']]);
        if ((int) $course['id_diretor_curso'] !== $this->userId && !$this->db->one('rel_disciplina_user', ['id_user' => $this->userId, 'id_curso' => $class['id_curso'], 'id_disciplina' => $subjectId, 'cargo' => 'professor'])) { $this->deny(); }
        return $class;
    }

    public function member(int $course, int $user, ?string $role = null): array
    {
        $account = $this->db->one('users', ['id' => $user, 'ativo' => 1]);
        if (!$account || ($role !== null && $account['cargo'] !== $role) || !$this->db->one('rel_user_curso', ['id_curso' => $course, 'id_user' => $user, 'estado' => '1'])) { throw new HttpException('Utilizador não pertence ao curso', 422); }
        return $account;
    }

    private function deny(): never { throw new HttpException('Não tem permissão para esta operação', 403); }
}
