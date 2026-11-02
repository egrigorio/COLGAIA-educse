<?php

require_once dirname(__DIR__, 2) . '/bootstrap/management.php';
require_form_post();
try {
    $input = new Educse\Http\Input($_POST);
    $db = new Educse\Repositories\SchoolRepository();
    $policy = new Educse\Policies\SchoolPolicy((int) ($_SESSION['id'] ?? 0), $db);
    $reply = (new Educse\Services\AssistantService($db, $policy))->reply($input->int('id_turma'), $input->string('message', 4000));
    header('Content-Type: text/plain; charset=utf-8');
    echo $reply;
} catch (Educse\Exceptions\HttpException $exception) {
    http_response_code($exception->status);
    exit($exception->getMessage());
} catch (Throwable $exception) {
    error_log('Falha no assistente: ' . get_class($exception));
    http_response_code(502);
    exit('O assistente está indisponível. Tente novamente.');
}
