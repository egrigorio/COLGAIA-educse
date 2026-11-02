<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

function env_value(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? (getenv($key) === false ? $default : getenv($key));
}

function app_base_path(string $scriptName): string
{
    $path = str_replace('\\', '/', $scriptName);
    foreach (['/admin/', '/modules/', '/pages/', '/manual/'] as $directory) {
        $position = strpos($path, $directory);
        if ($position !== false) {
            return substr($path, 0, $position);
        }
    }
    return rtrim(str_replace('\\', '/', dirname($path)), '/.');
}

error_reporting(E_ALL);
ini_set('display_errors', filter_var(env_value('APP_DEBUG', false), FILTER_VALIDATE_BOOL) ? '1' : '0');
date_default_timezone_set((string) env_value('APP_TIMEZONE', 'Europe/Lisbon'));

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
    ]);
}

require_once dirname(__DIR__) . '/app/http.php';
