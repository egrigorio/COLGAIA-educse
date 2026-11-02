<?php

require_once dirname(__DIR__) . '/bootstrap/management.php';
require_form_post();
$users = new Educse\Repositories\UserRepository();

if (post_string('tipo') === '1') {
    $email = trim(post_string('email'));
    unset($_SESSION['token_password'], $_SESSION['email_user_recuperar_pass'], $_SESSION['token_password_expira']);
    $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? $users->findByIdentity($email) : null;
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $baseUrl = (string) env_value('APP_URL', '');
        if (!preg_match('~^https?://~i', $baseUrl)) {
            auth_error('Configure APP_URL para enviar links de recuperação.', 'email_recuperar_senha.php');
        }
        try {
            recuperar_senha($email, $user['username'], $arrConfig['url_paginas'] . 'auth/recuperar_senha.php?token=' . $token);
        } catch (RuntimeException $exception) {
            auth_error('Não foi possível enviar o email. Tente novamente.', 'email_recuperar_senha.php');
        }
        $_SESSION['token_password'] = hash('sha256', $token);
        $_SESSION['email_user_recuperar_pass'] = $email;
        $_SESSION['token_password_expira'] = time() + 1800;
    }
    $_SESSION['msg_sucesso'] = 'Se a conta existir, receberá um email com as instruções.';
    redirect_to($arrConfig['url_paginas'] . 'auth/email_recuperar_senha.php');
}

$token = post_string('token');
if (post_string('tipo') !== '2' || $token === '' || !isset($_SESSION['token_password'], $_SESSION['email_user_recuperar_pass']) || time() >= ($_SESSION['token_password_expira'] ?? 0) || !hash_equals($_SESSION['token_password'], hash('sha256', $token))) {
    auth_error('Token inválido ou expirado', 'email_recuperar_senha.php');
}
$password = post_string('password');
if ($password === '' || $password !== post_string('password2')) {
    $_SESSION['erro'] = 'As passwords não coincidem ou estão vazias';
    redirect_to($arrConfig['url_paginas'] . 'auth/recuperar_senha.php?token=' . rawurlencode($token));
}
$users->changePassword($_SESSION['email_user_recuperar_pass'], password_hash($password, PASSWORD_DEFAULT));
unset($_SESSION['token_password'], $_SESSION['email_user_recuperar_pass'], $_SESSION['token_password_expira']);
$_SESSION['msg_sucesso'] = 'Password alterada com sucesso';
redirect_to($arrConfig['url_paginas'] . 'auth/login.php');
