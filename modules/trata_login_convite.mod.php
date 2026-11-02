<?php

require_once dirname(__DIR__) . '/bootstrap/management.php';
require_form_post();
try {
    $user = (new Educse\Services\AuthService())->login(post_string('user_or_mail'), post_string('password'));
    (new Educse\Services\InvitationService(new Educse\Repositories\SchoolRepository()))->accept(
        is_string($_SESSION['convite_token'] ?? null) ? $_SESSION['convite_token'] : '', (int) $user['id']
    );
    Educse\Http\InvitationController::clear();
    Educse\Services\AuthService::startSession($user);
    redirect_to($arrConfig['url_admin']);
} catch (DomainException|Educse\Exceptions\HttpException $exception) {
    if ($exception instanceof Educse\Exceptions\HttpException && $exception->status === 404) { Educse\Http\InvitationController::clear(); }
    auth_error($exception->getMessage());
}
