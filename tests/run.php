<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

$checks = 0;
function check(bool $condition, string $message): void
{
    global $checks;
    $checks++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
function rejects(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (DomainException $exception) {
        check($exception->getMessage() === $message, 'Erro inesperado: ' . $exception->getMessage());
        return;
    }
    throw new RuntimeException('A operação deveria ter sido rejeitada');
}

ob_start();
require_once dirname(__DIR__) . '/include/config.inc.php';
check(ob_get_clean() === '', 'O bootstrap não deve enviar conteúdo antes dos cabeçalhos HTTP');

foreach ([
    '/index.php' => '', '/admin/index.php' => '', '/pages/auth/login.php' => '',
    '/educse/index.php' => '/educse', '/educse/admin/editar/atividade_turma.php' => '/educse',
    '/educse/pages/auth/login.php' => '/educse', '/educse/modules/trata_login.mod.php' => '/educse',
] as $script => $expected) {
    check(app_base_path($script) === $expected, 'Base URL incorreta: ' . $script);
}
$first = csrf_field();
check($first === csrf_field(), 'O token deve manter-se entre formulários da mesma sessão');
check(strlen($_SESSION['csrf_token']) === 64, 'O token deve conter 32 bytes aleatórios');
$form = csrf_forms('<form method="POST"><button>Guardar</button></form>');
check(substr_count(csrf_forms($form), 'name="csrf_token"') === 1, 'Adicionar CSRF sem duplicar campos em vistas compostas');
$_POST = ['field' => ['array'], 'valid' => 'valor'];
check(post_string('field') === '' && post_string('missing') === '' && post_string('valid') === 'valor', 'Entrada de formulário inválida');
$auth = new Educse\Services\AuthService();
rejects(fn () => $auth->login('', ''), 'Preencha todos os campos');
rejects(fn () => $auth->prepareRegistration('invalid', 'user', 'pass', 'pass', 'aluno'), 'Email inválido');
rejects(fn () => $auth->prepareRegistration('test@example.com', 'user', 'pass', 'pass', 'admin'), 'Cargo inválido');
rejects(fn () => $auth->prepareRegistration('test@example.com', 'user', 'pass', 'other', 'aluno'), 'As passwords não coincidem');

if (getenv('EDUCSE_TEST_DATABASE') === '1') {
    require dirname(__DIR__) . '/include/config.inc.php';
    // A tabela temporária só existe nesta ligação e não altera tabelas persistentes.
    my_query('CREATE TEMPORARY TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255), email VARCHAR(255),
        password VARCHAR(255), cargo VARCHAR(40), pfp VARCHAR(255), ativo INT,
        ultimo_login DATETIME NULL
    )');
    $users = new Educse\Repositories\UserRepository();
    $account = $auth->prepareRegistration('test@example.com', "O\'Brien", 'test password', 'test password', 'aluno');
    $id = $users->create($account);
    check($id > 0, 'O INSERT deve devolver o id');
    check($users->exists('test@example.com', 'other'), 'Deve encontrar email duplicado');
    check($users->findByIdentity("' OR 1=1 --") === null, 'Não deve interpretar SQL no username');
    rejects(fn () => $auth->login("' OR 1=1 --", 'test password'), 'Credenciais incorretas');
    rejects(fn () => $auth->login("O\'Brien", 'wrong'), 'Credenciais incorretas');
    rejects(fn () => $auth->prepareRegistration('new@example.com', "O\'Brien", 'pass', 'pass', 'aluno'), 'Utilizador já existe');
    $user = $auth->login('test@example.com', 'test password');
    check($user['username'] === "O\'Brien", 'Login por email deve resolver o username');
    $sessionId = session_id();
    Educse\Services\AuthService::startSession($user);
    check(session_id() !== $sessionId && $_SESSION['id'] === $id, 'Deve regenerar a sessão no login');
    check(!isset($_SESSION['csrf_token']), 'Login deve renovar o token CSRF');
    check($auth->login("O\'Brien", 'test password')['id'] === $id, 'Login por username deve funcionar');
    check($users->findByIdentity('test@example.com')['ultimo_login'] !== null, 'Deve atualizar o último login');
    my_query('UPDATE users SET ativo = 0 WHERE id = ?', 0, [$id]);
    rejects(fn () => $auth->login('test@example.com', 'test password'), 'Conta não verificada');
    my_query('UPDATE users SET ativo = 1 WHERE id = ?', 0, [$id]);
    $users->changePassword('test@example.com', password_hash('new password', PASSWORD_DEFAULT));
    rejects(fn () => $auth->login('test@example.com', 'test password'), 'Credenciais incorretas');
    check($auth->login('test@example.com', 'new password')['id'] === $id, 'Deve aceitar a nova password');
    foreach (['verify_success', 'verify_wrong', 'verify_expired', 'verify_locked', 'reset_success', 'reset_wrong', 'reset_expired', 'reset_mismatch'] as $scenario) {
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/auth_endpoints.php') . ' ' . escapeshellarg($scenario), $status);
        check($status === 0, 'O fluxo falhou: ' . $scenario);
    }
    require __DIR__ . '/management.php';
} else {
    echo "Integração MySQL não executada (defina EDUCSE_TEST_DATABASE=1 e DB_*).\n";
}
echo "$checks verificações passaram.\n";
