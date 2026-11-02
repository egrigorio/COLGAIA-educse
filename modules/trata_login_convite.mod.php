<?php

require_once dirname(__DIR__) . '/include/config.inc.php';
require_form_post();
try {
    $user = (new Educse\Services\AuthService())->login(post_string('user_or_mail'), post_string('password'));
    Educse\Services\AuthService::startSession($user);
} catch (DomainException $exception) {
    auth_error($exception->getMessage());
}
    if(isset($_SESSION['id_user'], $_SESSION['id_curso'], $_SESSION['convite']) && (int) $_SESSION['id_user'] === $_SESSION['id']) {
        $id_user = (int) $_SESSION['id_user'];
        $id_curso = (int) $_SESSION['id_curso'];
        if($_SESSION['dc']) {
            $sql = "UPDATE curso SET id_diretor_curso = $id_user WHERE id = $id_curso";
            my_query($sql);    
            $sql = "UPDATE curso SET ativo = 1 WHERE id = $id_curso";
            my_query($sql);
            $sql = "SELECT * FROM turma WHERE id_curso = $id_curso";
            $res = my_query($sql);
            if(count($res) > 0) {
                foreach($res as $turma) {
                    $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES (" . $turma['id'] . ", $id_user)";
                    $id_inserido = my_query($sql);
                    $sql = "INSERT INTO rel_turno_user (id_turno, id_rel_turma_user) VALUES ( -1, $id_inserido)";
                    my_query($sql);

                }
            }
        } else {
            $sql = "UPDATE rel_user_curso SET estado = '1' WHERE id_user = $id_user AND id_curso = $id_curso";
            $res = my_query($sql);            
        }
        unset($_SESSION['id_user']);
        unset($_SESSION['email']);
        unset($_SESSION['id_curso']);
        unset($_SESSION['dc']);
        $sql = "DELETE FROM conf_convite WHERE id = " . (int) $_SESSION['convite'];
        $res = my_query($sql);
        unset($_SESSION['convite']);      
    }
redirect_to($arrConfig['url_admin'] . 'index.php');
