<?php

namespace Educse\Http;

use Educse\Exceptions\HttpException;
use Educse\Repositories\SchoolRepository;
use Educse\Services\InvitationService;

final class InvitationController
{
    public static function clear(): void
    {
        if (!isset($_SESSION['id'])) { unset($_SESSION['cargo']); }
        foreach (['convite_token', 'convite', 'id_user', 'email', 'id_curso', 'dc'] as $key) { unset($_SESSION[$key]); }
    }

    public static function run(): never
    {
        global $arrConfig;
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? '';
            if (!in_array($method, ['GET', 'POST'], true)) { throw new HttpException('Método não permitido', 405); }
            if ($method === 'POST') { \require_form_post(); }
            $token = (new Input($method === 'POST' ? $_POST : $_GET))->string('token', 64);
            $service = new InvitationService(new SchoolRepository());
            $invitation = $service->find($token);
            if (!isset($_SESSION['id'])) {
                self::clear();
                $_SESSION['convite_token'] = $token;
                $_SESSION['id_curso'] = (int) $invitation['id_curso'];
                $_SESSION['email'] = $invitation['email'];
                $_SESSION['cargo'] = $invitation['cargo'] === 'Diretor de Curso' ? 'professor' : $invitation['cargo'];
                $user = (new SchoolRepository())->one('users', ['email' => $invitation['email']]);
                \redirect_to($arrConfig['url_paginas'] . 'auth/' . ($user ? 'login.php' : 'registo.php'));
            }
            if ($method === 'GET') {
                $user = (new SchoolRepository())->one('users', ['id' => (int) $_SESSION['id']]);
                if (!$user || strcasecmp($user['email'], $invitation['email']) !== 0) { throw new HttpException('O convite pertence a outra conta', 403); }
                header('Content-Type: text/html; charset=utf-8');
                echo '<!doctype html><html lang="pt"><meta charset="utf-8"><title>Aceitar convite</title><form method="post">' . \csrf_field() . '<input type="hidden" name="token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '"><p>Deseja aceitar o convite para o curso?</p><button>Aceitar convite</button></form></html>';
                exit;
            }
            $service->accept($token, (int) $_SESSION['id']);
            self::clear();
            $_SESSION['convite_aceite'] = true;
            \redirect_to($arrConfig['url_admin']);
        } catch (HttpException $exception) {
            if ($exception->status === 404) { self::clear(); }
            http_response_code($exception->status);
            header('Content-Type: text/plain; charset=utf-8');
            exit($exception->getMessage());
        } catch (\Throwable $exception) {
            \error_log('Falha ao processar convite: ' . get_class($exception));
            http_response_code(500);
            exit('Não foi possível processar o convite. Tente novamente.');
        }
    }
}
