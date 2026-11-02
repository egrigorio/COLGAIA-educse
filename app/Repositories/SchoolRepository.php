<?php

namespace Educse\Repositories;

/** SQL fica nesta camada. Os identificadores são constantes internas, nunca nomes vindos do pedido. */
final class SchoolRepository
{
    private static int $transactionDepth = 0;

    private const COLUMNS = [
        'users' => 'id username email password cargo pfp ativo ultimo_login',
        'instituicao' => 'id nome id_dono ativo',
        'curso' => 'id nome_curso abreviatura duracao id_diretor_curso ativo',
        'turma' => 'id nome_turma id_curso ano_letivo ano id_esforco id_diretor_turma ativo',
        'esforco' => 'id limite barreira dia_0 dia_1 dia_2 dia_3 dia_4 dia_5 dia_6 ativo',
        'disciplinas' => 'id nome abreviatura ativo',
        'turno' => 'id numero ativo',
        'eventos' => 'id titulo comeco fim ativo',
        'atividades' => 'id id_evento descricao tipo id_professor tempo_sugerido id_disciplina id_turno ativo',
        'eventos_alunos_calendario' => 'id id_user start end title description backgroundColor',
        'conf_convite' => 'id email id_curso cargo token_hash expira_em criado_em',
        'rel_instituicao_curso' => 'id id_instituicao id_curso',
        'rel_instituicao_disciplinas' => 'id id_instituicao id_disc',
        'rel_user_curso' => 'id id_user id_curso estado',
        'rel_turma_user' => 'id id_turma id_user ativo',
        'rel_turno_user' => 'id id_turno id_rel_turma_user',
        'rel_disciplina_curso' => 'id id_disciplina id_curso',
        'rel_disciplina_curso_ano' => 'id id_disciplina id_curso ano',
        'rel_disciplina_turma' => 'id id_disciplina id_turma',
        'rel_disciplina_user' => 'id id_user id_disciplina id_curso cargo',
        'rel_atividades_turma' => 'id id_atividade id_turma',
    ];

    private function table(string $table): string
    {
        if (!isset(self::COLUMNS[$table])) { throw new \LogicException('Tabela não permitida'); }
        return '`' . $table . '`';
    }

    private function column(string $table, string $column): string
    {
        $this->table($table);
        if (!in_array($column, explode(' ', self::COLUMNS[$table]), true)) { throw new \LogicException('Coluna não permitida'); }
        return '`' . $column . '`';
    }

    private function where(string $table, array $criteria, array &$params): string
    {
        $parts = [];
        foreach ($criteria as $key => $value) {
            $column = $this->column($table, $key);
            if (is_array($value)) {
                $parts[] = $value === [] ? '0 = 1' : $column . ' IN (' . implode(',', array_fill(0, count($value), '?')) . ')';
                array_push($params, ...$value);
            } elseif ($value === null) {
                $parts[] = "$column IS NULL";
            } else {
                $parts[] = "$column = ?";
                $params[] = $value;
            }
        }
        return $parts === [] ? '1 = 1' : implode(' AND ', $parts);
    }

    public function all(string $table, array $criteria = []): array
    {
        $params = [];
        $where = $this->where($table, $criteria, $params);
        return \my_query('SELECT * FROM ' . $this->table($table) . ' WHERE ' . $where, 0, $params);
    }

    public function one(string $table, array $criteria, bool $lock = false): ?array
    {
        $params = [];
        $where = $this->where($table, $criteria, $params);
        $rows = \my_query('SELECT * FROM ' . $this->table($table) . ' WHERE ' . $where . ' LIMIT 1' . ($lock ? ' FOR UPDATE' : ''), 0, $params);
        return $rows[0] ?? null;
    }

    public function insert(string $table, array $data): int
    {
        $columns = array_map(fn ($key) => $this->column($table, $key), array_keys($data));
        $sql = 'INSERT INTO ' . $this->table($table) . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')';
        \my_query($sql, 0, array_values($data));
        return \db_connection()->insert_id;
    }

    public function update(string $table, array $data, array $criteria): void
    {
        if ($criteria === [] || $data === []) { throw new \LogicException('UPDATE sem filtro ou valores'); }
        $params = array_values($data);
        $set = array_map(fn ($key) => $this->column($table, $key) . ' = ?', array_keys($data));
        $where = $this->where($table, $criteria, $params);
        \my_query('UPDATE ' . $this->table($table) . ' SET ' . implode(',', $set) . ' WHERE ' . $where, 0, $params);
    }

    public function delete(string $table, array $criteria): void
    {
        if ($criteria === []) { throw new \LogicException('DELETE sem filtro'); }
        $params = [];
        $where = $this->where($table, $criteria, $params);
        \my_query('DELETE FROM ' . $this->table($table) . ' WHERE ' . $where, 0, $params);
    }

    public function ensure(string $table, array $criteria, array $defaults = []): int
    {
        return (int) (($this->one($table, $criteria)['id'] ?? null) ?: $this->insert($table, $criteria + $defaults));
    }

    public function transaction(callable $operation): mixed
    {
        if (self::$transactionDepth > 0) { return $operation(); }
        $connection = \db_connection();
        $connection->begin_transaction();
        self::$transactionDepth++;
        try {
            $result = $operation();
            $connection->commit();
            return $result;
        } catch (\Throwable $exception) {
            $connection->rollback();
            throw $exception;
        } finally {
            self::$transactionDepth--;
        }
    }

    public function membershipsInCourse(int $course, ?int $user = null): array
    {
        return \my_query('SELECT r.* FROM rel_turma_user r JOIN turma t ON t.id = r.id_turma WHERE t.id_curso = ?' . ($user === null ? '' : ' AND r.id_user = ?'), 0, $user === null ? [$course] : [$course, $user]);
    }

    public function subjectsForInstitution(int $institution): array
    {
        return \my_query('SELECT DISTINCT d.* FROM disciplinas d JOIN rel_instituicao_disciplinas r ON r.id_disc = d.id WHERE r.id_instituicao = ?', 0, [$institution]);
    }

    public function institutionForCourse(int $course): ?array
    {
        $rows = \my_query('SELECT i.* FROM instituicao i JOIN rel_instituicao_curso r ON r.id_instituicao = i.id WHERE r.id_curso = ? LIMIT 1', 0, [$course]);
        return $rows[0] ?? null;
    }

    public function detachMemberships(array $ids): void
    {
        if ($ids === []) { return; }
        $this->delete('rel_turno_user', ['id_rel_turma_user' => $ids]);
        $this->delete('rel_turma_user', ['id' => $ids]);
    }

    public function turnIds(int $class): array
    {
        return array_map('intval', array_column(\my_query('SELECT DISTINCT rt.id_turno FROM rel_turno_user rt JOIN rel_turma_user r ON r.id = rt.id_rel_turma_user WHERE r.id_turma = ? AND r.ativo = 1 AND rt.id_turno > 0', 0, [$class]), 'id_turno'));
    }
    public function activityContext(int $class): array
    {
        return \my_query('SELECT e.*, a.descricao, a.tipo, a.tempo_sugerido, u.username AS nome_professor, d.nome AS nome_disciplina, t.numero AS numero_turno
            FROM atividades a JOIN rel_atividades_turma r ON r.id_atividade = a.id
            JOIN eventos e ON e.id = a.id_evento JOIN users u ON u.id = a.id_professor
            JOIN disciplinas d ON d.id = a.id_disciplina LEFT JOIN turno t ON t.id = a.id_turno
            WHERE r.id_turma = ?', 0, [$class]);
    }

}
