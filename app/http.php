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
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], post_string('csrf_token'))) {
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
