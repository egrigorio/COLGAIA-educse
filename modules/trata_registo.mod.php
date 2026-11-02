<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
require_form_post();

try {
    $account = (new Educse\Services\AuthService())->prepareRegistration(
        post_string('email'), post_string('user'), post_string('pass'),
        post_string('confirmar_pass'), post_string('cargo')
    );
    $code = (string) random_int(10000, 99999);
    // Só guardar o registo pendente se o envio do email terminar sem erro.
    email_verificacao($account['email'], $account['user'], $code);
    $_SESSION['tmp_acc'] = $account;
    $_SESSION['codigo'] = $code;
    $_SESSION['codigo_expira'] = time() + 900;
    $_SESSION['codigo_tentativas'] = 0;
    redirect_to($arrConfig['url_paginas'] . 'auth/verificar_email.php');
} catch (DomainException $exception) {
    auth_error($exception->getMessage(), 'registo.php');
} catch (RuntimeException $exception) {
    auth_error('Não foi possível enviar o email de verificação. Tente novamente.', 'registo.php');
}
