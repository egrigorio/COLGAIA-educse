<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';
$config = require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/include/db.inc.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$connection = new mysqli($config['servername'], $config['username'], $config['password'], '', $config['port']);
$database = '`' . str_replace('`', '``', $config['dbname']) . '`';
$connection->query("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$connection->select_db($config['dbname']);
if ($connection->query('SHOW TABLES')->num_rows !== 0) {
    fwrite(STDERR, "O banco já contém tabelas. A instalação só aceita um banco vazio.\n");
    exit(1);
}
$connection->multi_query(file_get_contents(dirname(__DIR__) . '/database/schema.sql'));
do {
    if ($result = $connection->store_result()) {
        $result->free();
    }
} while ($connection->more_results() && $connection->next_result());
echo "Esquema instalado: 22 tabelas e 4 views.\n";
