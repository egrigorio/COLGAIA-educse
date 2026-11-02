<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
require_form_post();
if (isset($_SESSION['id'])) {
    redirect_to($arrConfig['url_admin']);
}
if (!isset($_SESSION['tmp_acc'], $_SESSION['codigo'], $_SESSION['codigo_expira']) || time() >= $_SESSION['codigo_expira'] || ($_SESSION['codigo_tentativas'] ?? 0) >= 5) {
    unset($_SESSION['tmp_acc'], $_SESSION['codigo'], $_SESSION['codigo_expira'], $_SESSION['codigo_tentativas']);
    auth_error('Código expirado. Volte a efetuar o registo.', 'registo.php');
}
$_SESSION['codigo_tentativas']++;
if (!hash_equals((string) $_SESSION['codigo'], post_string('codigo'))) {
    auth_error('Código inválido', 'verificar_email.php');
}
$users = new Educse\Repositories\UserRepository();
$account = $_SESSION['tmp_acc'];
if ($users->exists($account['email'], $account['user'])) {
    auth_error('Utilizador já existe', 'registo.php');
}
$users->create($account);
unset($_SESSION['tmp_acc'], $_SESSION['codigo'], $_SESSION['codigo_expira'], $_SESSION['codigo_tentativas']);
$_SESSION['msg_sucesso'] = 'Conta criada com sucesso';
redirect_to($arrConfig['url_paginas'] . 'auth/login.php');
