<?php

function my_connect(array $config): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli(
        $config['servername'], $config['username'], $config['password'],
        $config['dbname'], $config['port'] ?? 3306
    );
    $connection->set_charset('utf8mb4');
    return $connection;
}

function db_connection(): mysqli
{
    global $arrConfig;
    // As páginas públicas não precisam de estabelecer ligação ao banco.
    return $arrConfig['conn'] ??= my_connect($arrConfig);
}

/** Mantém o contrato antigo: linhas para SELECT, id para INSERT e 1 para UPDATE/DELETE. */
function my_query(string $sql, int $debug = 0, array $params = []): array|int
{
    $connection = db_connection();
    $result = $params === [] ? $connection->query($sql) : $connection->execute_query($sql, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return $connection->insert_id ?: 1;
}
