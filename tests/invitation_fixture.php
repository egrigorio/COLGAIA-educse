<?php

require dirname(__DIR__) . '/bootstrap/management.php';
if (PHP_SAPI !== 'cli' || !str_contains((string) env_value('DB_DATABASE', ''), 'test')) {
    exit('Use apenas um banco descartável com test no nome.');
}
$db = new Educse\Repositories\SchoolRepository();
$user = $db->one('users', ['username' => 'demo_aluno']);
$membership = $user ? $db->one('rel_user_curso', ['id_user' => $user['id'], 'estado' => '1']) : null;
if (!$membership) { throw new RuntimeException('Instale os dados demonstrativos antes do teste.'); }
$token = bin2hex(random_bytes(32));
$db->insert('conf_convite', ['email' => $user['email'], 'id_curso' => $membership['id_curso'], 'cargo' => 'aluno', 'token_hash' => hash('sha256', $token), 'expira_em' => date('Y-m-d H:i:s', time() + 600)]);
echo json_encode(['token' => $token]);
