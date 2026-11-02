<?php

namespace Educse\Services;

use Educse\Exceptions\HttpException;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

final class InvitationService
{
    private \Closure $mailer;
    public function __construct(private SchoolRepository $db, private ?SchoolPolicy $policy = null, ?callable $mailer = null)
    {
        $this->mailer = $mailer !== null ? \Closure::fromCallable($mailer) : static function ($email, $url, $role, $course, $existing) {
            $existing ? \enviar_convite_curso($email, $url, $role, $course) : \enviar_convite_plataforma($email, $url, $role, $course);
        };
    }

    public function issue(int $courseId, string $email, string $role): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['aluno', 'professor', 'Diretor de Curso'], true)) { throw new HttpException('Convite inválido'); }
        $base = rtrim((string) \env_value('APP_URL', ''), '/');
        if (!preg_match('~^https?://[^\s]+$~', $base)) { throw new HttpException('Configure APP_URL absoluta para enviar convites.'); }
        $token = bin2hex(random_bytes(32));
        $details = $this->db->transaction(function () use ($courseId, $email, $role, $token) {
            if (!$this->policy) { throw new \LogicException('Convite sem autor'); }
            $course = $this->policy->course($courseId, $role === 'Diretor de Curso');
            if ($role === 'Diretor de Curso') { $this->policy->institution(); }
            $this->db->one('curso', ['id' => $courseId], true);
            $user = $this->db->one('users', ['email' => $email]);
            $expected = $role === 'Diretor de Curso' ? 'professor' : $role;
            if ($user && (!$user['ativo'] || $user['cargo'] !== $expected)) { throw new HttpException('O cargo da conta não corresponde ao convite.'); }
            $member = $user ? $this->db->one('rel_user_curso', ['id_curso' => $courseId, 'id_user' => $user['id']]) : null;
            if ($member && $member['estado'] === '1' && $role !== 'Diretor de Curso') { throw new HttpException('Utilizador já pertence ao curso.'); }
            $this->db->delete('conf_convite', ['id_curso' => $courseId, 'email' => $email]);
            $id = $this->db->insert('conf_convite', ['email' => $email, 'id_curso' => $courseId, 'cargo' => $role, 'token_hash' => hash('sha256', $token), 'expira_em' => date('Y-m-d H:i:s', time() + 7 * 86400)]);
            if ($user && !$member) { $this->db->insert('rel_user_curso', ['id_curso' => $courseId, 'id_user' => $user['id'], 'estado' => 'Convite enviado']); }
            return ['id' => $id, 'course' => $course['nome_curso'], 'existing' => $user !== null];
        });
        $url = $base . '/modules/trata_convite_user_curso.mod.php?token=' . rawurlencode($token);
        try { ($this->mailer)($email, $url, $role, $details['course'], $details['existing']); }
        catch (\Throwable $exception) {
            \error_log('Falha de envio do convite ' . $details['id'] . ': ' . get_class($exception));
            throw new HttpException('O convite foi guardado, mas o email não foi enviado. Verifique o SMTP e tente novamente.', 502);
        }
    }

    public function find(string $token, bool $lock = false): array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) { throw new HttpException('Convite inválido ou expirado', 404); }
        $invitation = $this->db->one('conf_convite', ['token_hash' => hash('sha256', $token)], $lock);
        if (!$invitation || empty($invitation['expira_em']) || strtotime($invitation['expira_em']) <= time() || !$this->db->one('curso', ['id' => $invitation['id_curso']])) { throw new HttpException('Convite inválido ou expirado', 404); }
        return $invitation;
    }

    public function accept(string $token, int $userId): void
    {
        $this->db->transaction(function () use ($token, $userId) {
            $invitation = $this->find($token, true);
            $user = $this->db->one('users', ['id' => $userId, 'ativo' => 1]);
            $role = $invitation['cargo'] === 'Diretor de Curso' ? 'professor' : $invitation['cargo'];
            if (!$user || strcasecmp($user['email'], $invitation['email']) !== 0 || $user['cargo'] !== $role) { throw new HttpException('O convite pertence a outra conta', 403); }
            $courseId = (int) $invitation['id_curso'];
            $this->db->one('curso', ['id' => $courseId], true);
            if ($invitation['cargo'] === 'Diretor de Curso') {
                $other = $this->db->one('curso', ['id_diretor_curso' => $userId]);
                if ($other && (int) $other['id'] !== $courseId) { throw new HttpException('Professor já dirige outro curso.'); }
                $this->db->update('curso', ['id_diretor_curso' => $userId, 'ativo' => 1], ['id' => $courseId]);
                foreach ($this->db->all('turma', ['id_curso' => $courseId, 'ano_letivo' => [CourseService::currentYear(), CourseService::nextYear(CourseService::currentYear())]]) as $class) {
                    $member = $this->db->ensure('rel_turma_user', ['id_user' => $userId, 'id_turma' => $class['id']], ['ativo' => 1]);
                    $this->db->update('rel_turma_user', ['ativo' => 1], ['id' => $member]);
                    if (!$this->db->one('rel_turno_user', ['id_rel_turma_user' => $member])) { $this->db->insert('rel_turno_user', ['id_rel_turma_user' => $member, 'id_turno' => -1]); }
                }
            }
            $member = $this->db->ensure('rel_user_curso', ['id_user' => $userId, 'id_curso' => $courseId], ['estado' => '1']);
            $this->db->update('rel_user_curso', ['estado' => '1'], ['id' => $member]);
            $this->db->delete('conf_convite', ['id' => $invitation['id']]);
        });
    }

    public function cancel(int $id): void
    {
        $this->db->transaction(function () use ($id) {
            $invitation = $this->db->one('conf_convite', ['id' => $id]) ?? throw new HttpException('Convite não encontrado', 404);
            $this->policy?->course((int) $invitation['id_curso'], true) ?? throw new HttpException('Sem autorização', 403);
            $this->db->delete('conf_convite', ['id' => $id]);
            $user = $this->db->one('users', ['email' => $invitation['email']]);
            if ($user) { $this->db->delete('rel_user_curso', ['id_user' => $user['id'], 'id_curso' => $invitation['id_curso'], 'estado' => 'Convite enviado']); }
        });
    }
}
