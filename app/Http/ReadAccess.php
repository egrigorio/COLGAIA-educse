<?php

namespace Educse\Http;

use Educse\Exceptions\HttpException;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

/** Ponte das vistas antigas: valida o contexto antes de estas executarem consultas. */
final class ReadAccess
{
    public static function require(string $scope): void
    {
        try {
            $db = new SchoolRepository();
            $policy = new SchoolPolicy((int) ($_SESSION['id'] ?? 0), $db);
            $policy->actor();
            $input = new Input($_GET);
            if ($scope === 'classroom') {
                $class = $policy->classroom($input->int('id_turma'));
                $_GET['id_turma'] = (int) $class['id'];
                $_SESSION['id_curso'] = (int) $class['id_curso'];
                if ($input->has('id_evento') && !$db->one('rel_atividades_turma', ['id_turma' => $class['id'], 'id_atividade' => array_column($db->all('atividades', ['id_evento' => $input->int('id_evento')]), 'id')])) { throw new HttpException('Atividade não pertence à turma', 403); }
            } elseif ($scope === 'course') {
                $courseId = (new Input(['id' => $_GET['id_curso'] ?? $_SESSION['id_curso'] ?? null]))->int('id');
                $course = $policy->course($courseId);
                $_SESSION['id_curso'] = (int) $course['id'];
            } elseif ($scope === 'institution') {
                $_SESSION['id_instituicao'] = (int) $policy->institution()['id'];
            }
            foreach (['id_user', 'id_evento'] as $key) { if ($input->has($key)) { $_GET[$key] = $input->int($key); } }
            if ($input->has('ano_letivo') && !preg_match('~^\d{4}/\d{2}$~', $input->string('ano_letivo', 7))) { throw new HttpException('Ano letivo inválido'); }
            foreach (['turno', 'turno_numero'] as $key) {
                if ($input->has($key)) { $_GET[$key] = $input->string($key, 20) === 'all' ? 'all' : $input->int($key, -1, 100000); }
            }
        } catch (HttpException $exception) {
            http_response_code($exception->status);
            header('Content-Type: text/plain; charset=utf-8');
            exit($exception->getMessage());
        }
    }
}
