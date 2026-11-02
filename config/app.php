<?php

$root = dirname(__DIR__);
$url = rtrim((string) env_value('APP_URL', app_base_path($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
if ($url === '') {
    $url = app_base_path($_SERVER['SCRIPT_NAME'] ?? '/index.php');
}

return [
    'servername' => env_value('DB_HOST', '127.0.0.1'),
    'port' => (int) env_value('DB_PORT', 3306),
    'username' => env_value('DB_USERNAME', 'root'),
    'password' => env_value('DB_PASSWORD', ''),
    'dbname' => env_value('DB_DATABASE', 'educse'),
    'dir_site' => $root,
    'url_site' => $url,
    'dir_modules' => $root . '/modules/',
    'url_modules' => $url . '/modules/',
    'dir_admin' => $root . '/admin/',
    'url_admin' => $url . '/admin/',
    'dir_include' => $root . '/include/',
    'url_paginas' => $url . '/pages/',
    'url_public' => $url . '/public/',
    'url_pfp' => $url . '/public/pfp/',
    'dir_uploads' => $root . '/uploads/',
    'url_uploads' => $url . '/uploads/',
    'dir_img' => $root . '/images/',
    'url_img' => $url . '/images/',
    'auth_imgType' => ['image/jpeg', 'image/png', 'image/gif'],
];
