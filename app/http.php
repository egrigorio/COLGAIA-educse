<?php

function redirect_to(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function post_string(string $key): string
{
    return is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
}

function csrf_field(): string
{
    $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function require_form_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Método não permitido');
    }
    $token = post_string('csrf_token') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        exit('A sessão do formulário expirou. Recarregue a página.');
    }
}

function auth_error(string $message, string $page = 'login.php'): never
{
    global $arrConfig;
    $_SESSION['erro'] = $message;
    redirect_to($arrConfig['url_paginas'] . 'auth/' . $page);
}


/** Adapta apenas os formulários POST gerados pelas vistas legadas. */
function csrf_forms(string $html): string
{
    return preg_replace_callback('~<form\b[^>]*>(?!\s*<input type="hidden" name="csrf_token")~i', static function ($match) {
        if (!preg_match('~\bmethod=["\']post["\']~i', $match[0])) { return $match[0]; }
        $course = filter_var($_SESSION['id_curso'] ?? null, FILTER_VALIDATE_INT);
        return $match[0] . csrf_field() . ($course ? '<input type="hidden" name="id_curso" value="' . $course . '">' : '');
    }, $html);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
