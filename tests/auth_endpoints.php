<?php

// Executado em subprocessos: os controladores terminam com redirect_to().
require dirname(__DIR__) . '/include/config.inc.php';
my_query('CREATE TEMPORARY TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255), email VARCHAR(255),
    password VARCHAR(255), cargo VARCHAR(40), pfp VARCHAR(255), ativo INT, ultimo_login DATETIME NULL
)');
$users = new Educse\Repositories\UserRepository();
$account = ['user' => 'tester', 'email' => 'test@example.com', 'pass' => password_hash('old password', PASSWORD_DEFAULT), 'cargo' => 'aluno', 'pfp' => 'e.png'];
$mode = $argv[1];
$_SERVER['REQUEST_METHOD'] = 'POST';
csrf_field();
$_POST = ['csrf_token' => $_SESSION['csrf_token']];

if (str_starts_with($mode, 'verify_')) {
    $_SESSION['tmp_acc'] = $account;
    $_SESSION['codigo'] = '12345';
    $_SESSION['codigo_expira'] = time() + ($mode === 'verify_expired' ? -1 : 900);
    $_SESSION['codigo_tentativas'] = $mode === 'verify_locked' ? 5 : 0;
    $_POST['codigo'] = $mode === 'verify_wrong' ? '99999' : '12345';
    $handler = 'trata_verifconta.mod.php';
} else {
    $users->create($account);
    $_SESSION['token_password'] = hash('sha256', 'test-token');
    $_SESSION['email_user_recuperar_pass'] = $account['email'];
    $_SESSION['token_password_expira'] = time() + ($mode === 'reset_expired' ? -1 : 1800);
    $_POST += ['tipo' => '2', 'token' => $mode === 'reset_wrong' ? 'bad-token' : 'test-token', 'password' => 'new password', 'password2' => 'new password'];
    if ($mode === 'reset_mismatch') {
        $_POST['password2'] = 'other password';
    }
    $handler = 'trata_recuperar_senha.mod.php';
}

register_shutdown_function(static function () use ($mode, $users, $account): void {
    $user = $users->findByIdentity($account['email']);
    $passed = match ($mode) {
        'verify_success' => $user !== null && !isset($_SESSION['tmp_acc'], $_SESSION['codigo']),
        'verify_wrong' => $user === null && ($_SESSION['erro'] ?? '') === 'Código inválido',
        'verify_expired', 'verify_locked' => $user === null && !isset($_SESSION['tmp_acc']),
        'reset_success' => password_verify('new password', $user['password']) && !isset($_SESSION['token_password']),
        'reset_wrong', 'reset_expired' => password_verify('old password', $user['password']) && ($_SESSION['erro'] ?? '') === 'Token inválido ou expirado',
        'reset_mismatch' => password_verify('old password', $user['password']) && isset($_SESSION['erro']),
        default => false,
    };
    if (!$passed) {
        fwrite(STDERR, "Falhou: $mode\n");
        exit(1);
    }
    echo "$mode passou.\n";
});
require dirname(__DIR__) . '/modules/' . $handler;
