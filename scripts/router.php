<?php

// Router exclusivo do servidor de desenvolvimento do PHP.
$root = dirname(__DIR__);
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = realpath($root . ($path === '/' ? '/index.php' : $path));
$relative = $file === false ? '' : substr($file, strlen($root) + 1);
$first = explode('/', $relative)[0];
if ($file === false || !str_starts_with($file, $root . '/') || preg_match('~(^|/)\.~', $relative) || in_array($first, ['app', 'bootstrap', 'config', 'include', 'metodos', 'vendor', 'node_modules', 'tests', 'scripts', 'docs'], true)) {
    http_response_code(404);
    exit('Não encontrado');
}
if (is_dir($file)) {
    $file .= '/index.php';
    if (!is_file($file)) {
        http_response_code(404);
        exit('Não encontrado');
    }
}
if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    $_SERVER['SCRIPT_NAME'] = '/' . substr($file, strlen($root) + 1);
    require $file;
    return true;
}
if ($first === 'public' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['css', 'js', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'ico', 'otf', 'woff', 'woff2', 'svg'], true)) {
    return false;
}
http_response_code(404);
echo 'Não encontrado';
