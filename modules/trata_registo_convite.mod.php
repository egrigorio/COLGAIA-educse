<?php

require_once dirname(__DIR__) . '/bootstrap/management.php';
require_form_post();
try {
    $db = new Educse\Repositories\SchoolRepository();
    $invitations = new Educse\Services\InvitationService($db);
    $token = is_string($_SESSION['convite_token'] ?? null) ? $_SESSION['convite_token'] : '';
    $invitation = $invitations->find($token);
    $role = $invitation['cargo'] === 'Diretor de Curso' ? 'professor' : $invitation['cargo'];
    $account = (new Educse\Services\AuthService())->prepareRegistration(
        $invitation['email'], post_string('user'), post_string('pass'), post_string('confirmar_pass'), $role
    );
    $id = $db->transaction(function () use ($account, $invitations, $token) {
        $id = (new Educse\Repositories\UserRepository())->create($account);
        $invitations->accept($token, $id);
        return $id;
    });
    Educse\Http\InvitationController::clear();
    Educse\Services\AuthService::startSession(['id' => $id, 'username' => $account['user'], 'cargo' => $role, 'pfp' => $account['pfp'], 'ultimo_login' => null]);
    (new Educse\Repositories\UserRepository())->recordLogin($id);
    redirect_to($arrConfig['url_admin']);
} catch (DomainException|Educse\Exceptions\HttpException $exception) {
    if ($exception instanceof Educse\Exceptions\HttpException && $exception->status === 404) { Educse\Http\InvitationController::clear(); }
    auth_error($exception->getMessage(), 'registo.php');
}
