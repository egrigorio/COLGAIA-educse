<?php

require dirname(__DIR__) . '/bootstrap/management.php';
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$connection = db_connection();
$columns = array_column($connection->query('SHOW COLUMNS FROM conf_convite')->fetch_all(MYSQLI_ASSOC), 'Field');
if (!in_array('token_hash', $columns, true)) {
    $connection->query('ALTER TABLE conf_convite ADD token_hash CHAR(64) NULL, ADD UNIQUE KEY uq_convite_token (token_hash)');
}
if (!in_array('expira_em', $columns, true)) {
    $connection->query('ALTER TABLE conf_convite ADD expira_em DATETIME NULL');
}
echo "Migrações aplicadas. Convites antigos sem token deixam de ser aceites; envie novos convites.\n";
