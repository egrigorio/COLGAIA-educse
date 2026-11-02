<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
require_form_post();
if (!isset($_SESSION['email'], $_SESSION['cargo'], $_SESSION['id_curso'], $_SESSION['convite'])) {
    auth_error('Convite inválido', 'registo.php');
}
try {
    $account = (new Educse\Services\AuthService())->prepareRegistration(
        $_SESSION['email'], post_string('user'), post_string('pass'),
        post_string('confirmar_pass'), strtolower($_SESSION['cargo'])
    );
} catch (DomainException $exception) {
    auth_error($exception->getMessage(), 'registo.php');
}
$id_curso = (int) $_SESSION['id_curso'];
$id = (new Educse\Repositories\UserRepository())->create($account);
$sql = "SELECT * FROM rel_user_curso WHERE id_user = $id AND id_curso = $id_curso";
$res = my_query($sql);
if(count($res) == 0) {    
    
    if($_SESSION['dc']) {
        $sql = "UPDATE curso SET id_diretor_curso = $id WHERE id = $id_curso";
        my_query($sql);    
        $sql = "UPDATE curso SET ativo = 1 WHERE id = $id_curso";
        my_query($sql);
        $sql = "SELECT * FROM turma WHERE id_curso = $id_curso";
        $res = my_query($sql);
        if(count($res) > 0) {
            foreach($res as $r) {
                $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $r['id'] . ", $id)";
                $id_inserido = my_query($sql);
                $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ( -1, $id_inserido)";
                my_query($sql);

            }
        }
        $sql = "INSERT INTO rel_user_curso (id_user, id_curso, estado) VALUES ($id, $id_curso, '1')";
        my_query($sql);
    } else {
        $sql = "INSERT INTO rel_user_curso (id_user, id_curso, estado) VALUES ($id, $id_curso, '1')";
        my_query($sql);
    }
    unset($_SESSION['id_curso']);
    unset($_SESSION['email']);
    unset($_SESSION['dc']);
    unset($_SESSION['id_user']);
    $_SESSION['convite_aceite'] = 1;
    $sql = "DELETE FROM conf_convite WHERE id = " . (int) $_SESSION['convite'];
    $res = my_query($sql);
    unset($_SESSION['convite']);
} else {
    // tratar exceção de o user já estar na turma
}


Educse\Services\AuthService::startSession([
    'id' => $id, 'username' => $account['user'], 'cargo' => $account['cargo'],
    'pfp' => $account['pfp'], 'ultimo_login' => date('d/m/Y H:i'),
]);
(new Educse\Repositories\UserRepository())->recordLogin($id);
redirect_to($arrConfig['url_admin'] . 'index.php');
