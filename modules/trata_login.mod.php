<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
require_form_post();

try {
    $user = (new Educse\Services\AuthService())->login(post_string('user_or_mail'), post_string('password'));
    Educse\Services\AuthService::startSession($user);
    redirect_to($arrConfig['url_admin'] . 'index.php');
} catch (DomainException $exception) {
    auth_error($exception->getMessage());
}
