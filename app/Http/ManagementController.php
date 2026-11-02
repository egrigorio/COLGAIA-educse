<?php

namespace Educse\Http;

use Educse\Exceptions\HttpException;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;
use Educse\Services\{ActivityService, CourseService, InvitationService, MembershipService, SubjectService};

final class ManagementController
{
    private SchoolRepository $db;
    private SchoolPolicy $policy;
    private Input $input;
    public function __construct()
    {
        $this->db = new SchoolRepository();
        $this->policy = new SchoolPolicy((int) ($_SESSION['id'] ?? 0), $this->db);
        // Os parâmetros de rota antigos continuam aceites apenas num pedido POST com CSRF.
        $this->input = new Input(array_replace($_GET, $_POST));
    }

    private function courseId(): int
    {
        return (new Input(['id' => $_POST['id_curso'] ?? $_GET['id_curso'] ?? $_SESSION['id_curso'] ?? null]))->int('id');
    }

    private function editing(): bool
    {
        $mode = $this->input->string('tipo', 10);
        if (!in_array($mode, ['criar', 'editar'], true)) { throw new HttpException('Modo de edição inválido'); }
        return $mode === 'editar';
    }

    private function assignments(string $pattern, int $min = 1): array
    {
        $result = [];
        foreach ($this->input->fields($pattern) as $field) {
            $id = (int) $field['matches'][1];
            if ($id < 1) { throw new HttpException('Identificador inválido'); }
            $result[$id] = (new Input(['value' => $field['value']]))->int('value', $min);
        }
        return $result;
    }

    public function dispatch(string $action): string
    {
        $this->policy->actor();
        $i = $this->input;
        $activity = new ActivityService($this->db, $this->policy);
        $courses = new CourseService($this->db, $this->policy);
        $members = new MembershipService($this->db, $this->policy);
        $subjects = new SubjectService($this->db, $this->policy);
        switch ($action) {
            case 'editar_instituicao':
                $id = $courses->institution($i, $this->editing());
                $_SESSION['id_instituicao'] = $id;
                return 'instituicao.php';
            case 'editar_curso':
                $details = $courses->save($i, $this->editing());
                if (isset($details['email'])) { (new InvitationService($this->db, $this->policy))->issue($details['id'], $details['email'], $details['cargo']); }
                return 'instituicao.php?tab=cursos';
            case 'remover_curso_instituicao':
                $courses->remove($i->int('id_curso'));
                return 'instituicao.php?tab=cursos';
            case 'criar_atividade_turma': case 'editar_atividade_turma':
                $activity->save($i, $action === 'editar_atividade_turma');
                return 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=atividade';
            case 'remover_atividade':
                $activity->remove($i->int('id_evento'));
                return $i->has('id_turma') ? 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=atividade' : 'turma.php';
            case 'esforco_turma':
                $activity->effort($i);
                return 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=' . rawurlencode('esforço');
            case 'adicionar_eventos_ai':
                $activity->addPersonalEvents($i->string('eventos', 200000));
                return 'turma.php';
            case 'apagar_eventos_aluno':
                if ($this->policy->actor()['cargo'] !== 'aluno') { throw new HttpException('Perfil não autorizado', 403); }
                $this->db->delete('eventos_alunos_calendario', ['id_user' => $this->policy->id()]);
                return 'turma.php';
            case 'adicionar_disciplina_instituicao':
                $subjects->addToInstitution($i->list('disciplinas'));
                return 'instituicao.php?tab=disciplinas';
            case 'remover_disciplina_instituicao':
                $subjects->removeFromInstitution($i->int('id_disciplina'));
                return 'instituicao.php?tab=disciplinas';
            case 'adicionar_disciplina_curso':
                $subjects->addToCourse($this->courseId(), $i->ids('disciplinas'));
                return 'curso.php?tab=disciplinas';
            case 'excluir_disciplina_curso':
                $subjects->removeFromCourse($this->courseId(), $i->int('id_disc'));
                return 'curso.php?tab=disciplinas';
            case 'editar_disciplina_curso':
                $subjects->curriculum($this->courseId(), $i->fields('/^(\d+)%(\d+)$/'));
                return 'curso.php?tab=disciplinas';
            case 'editar_user_curso':
                $members->teacher($this->courseId(), $i->int('id_user'), $i->ids('Disciplina'), $i->ids('Turma'));
                return 'curso.php?tab=professores';
            case 'excluir_user_curso': case 'remover_user_curso':
                $members->remove($this->courseId(), $i->int('id_user'));
                return 'curso.php';
            case 'inserir_aluno_turma':
                $members->students($this->courseId(), $this->assignments('/^[^%]+%(\d+)$/'));
                return 'curso.php?tab=alunos';
            case 'editar_turma_diretor_curso':
                $members->directors($this->courseId(), $this->assignments('/^diretor_turma_(\d+)$/', -1));
                return 'curso.php?tab=turmas';
            case 'atualizar_turmas_ano_letivo':
                $advance = $i->has('tem_turmas') && $i->int('tem_turmas', 0, 1) === 1;
                $current = CourseService::currentYear();
                $courses->academicYear($this->courseId(), $current, $advance ? CourseService::nextYear($current) : $i->string('ano_letivo', 7), $advance);
                return 'curso.php?tab=turmas';
            case 'adicionar_turno_turma':
                $members->addTurn($i->int('id_turma'), $i->int('novo_turno', 1, 100));
                return 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=turno';
            case 'remover_turno_turma':
                $members->removeTurn($i->int('id_turma'), $i->int('id_turno'));
                return 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=turno';
            case 'editar_turno_user':
                $role = $i->string('cargo', 20);
                if ($role === 'alunos') {
                    $assignments = array_map(fn ($turn) => [$turn], $this->assignments('/^turno_(\d+)$/', -1));
                } elseif ($role === 'professores') {
                    $assignments = [];
                    foreach ($i->fields('/^id_user\$(\d+)$/') as $field) {
                        $id = (new Input(['id' => $field['value']]))->int('id');
                        $assignments[$id] = [];
                    }
                    foreach ($i->fields('/^turno_(\d+)%(\d+)$/') as $field) {
                        $user = (int) $field['matches'][1]; $turn = (int) $field['matches'][2];
                        if (!array_key_exists($user, $assignments)) { throw new HttpException('Professor inválido'); }
                        $assignments[$user][] = $turn;
                    }
                } else { throw new HttpException('Cargo inválido'); }
                $members->turns($i->int('id_turma'), $assignments, $role === 'alunos' ? 'aluno' : 'professor');
                return 'turma.php?id_turma=' . $i->int('id_turma') . '&tab=' . $role;
            case 'excluir_convite_user_curso':
                (new InvitationService($this->db, $this->policy))->cancel($i->int('id_user'));
                return 'curso.php';
            case 'adicionar_user_curso':
                $courseId = $this->courseId();
                $this->policy->course($courseId);
                $emails = $this->emails();
                $role = $i->string('cargo', 20);
                if (!in_array($role, ['aluno', 'professor'], true) || $emails === [] || count($emails) > 100) { throw new HttpException('Lista de convites inválida'); }
                foreach ($emails as $email) { if (!is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) { throw new HttpException('Email inválido na lista'); } }
                $invitations = new InvitationService($this->db, $this->policy);
                foreach (array_unique(array_map('trim', $emails)) as $email) { $invitations->issue($courseId, $email, $role); }
                return 'curso.php?tab=' . ($role === 'aluno' ? 'alunos' : 'professores');
            case 'inserir':
                if ($i->string('modulo', 40) !== 'turma') { throw new HttpException('Use o formulário específico para criar este recurso.'); }
                $id = $this->db->transaction(fn () => $courses->createClass($i->int('id_curso'), $i->string('ano_letivo', 7), $i->int('ano', 1, 12), $i->string('nome_turma', 100)));
                return 'turma.php?id_turma=' . $id;
            default: throw new HttpException('Operação não encontrada', 404);
        }
    }

    private function emails(): array
    {
        $emails = $this->input->list('emails');
        if (isset($_FILES['csv']) && $_FILES['csv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['csv'];
            if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 100000 || strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv' || !is_uploaded_file($file['tmp_name'])) { throw new HttpException('CSV inválido (máximo 100 KB)'); }
            $handle = fopen($file['tmp_name'], 'r');
            if (!$handle) { throw new HttpException('Não foi possível ler o CSV'); }
            try {
                while (($row = fgetcsv($handle, 1000, ';', '"', '')) !== false) {
                    if (count($row) !== 1) { throw new HttpException('O CSV deve ter um email por linha.'); }
                    $emails[] = $row[0];
                    if (count($emails) > 100) { throw new HttpException('Envie no máximo 100 convites.'); }
                }
            } finally { fclose($handle); }
        }
        return $emails;
    }

    public static function run(string $action): never
    {
        global $arrConfig;
        \require_form_post();
        $json = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
        try {
            $target = (new self())->dispatch($action);
            if ($json) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success' => true]); exit; }
            $_SESSION['management_success'] = 'Alterações guardadas com sucesso';
            \redirect_to($arrConfig['url_admin'] . $target);
        } catch (HttpException $exception) {
            http_response_code($exception->status);
            if ($json) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['error' => $exception->getMessage()]); exit; }
            header('Content-Type: text/html; charset=utf-8');
            echo '<p>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</p><a href="' . htmlspecialchars($arrConfig['url_admin'], ENT_QUOTES, 'UTF-8') . '">Voltar à gestão</a>';
            exit;
        } catch (\Throwable $exception) {
            \error_log('Falha na ação ' . $action . ': ' . get_class($exception));
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            exit(json_encode(['error' => 'Não foi possível concluir a operação. Tente novamente.']));
        }
    }
}
