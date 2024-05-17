<?php include '../include/config.inc.php';
pr($_POST);

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$sql = "SELECT id FROM users WHERE email = '{$_POST['diretor_curso']}'";
$res = my_query($sql);
if(count($res) > 0) {
    $sql = "SELECT email FROM users WHERE id = {$_SESSION['id']}";
    $res = my_query($sql);
    $email = $res[0]['email'];
    if($email == $_POST['diretor_curso']) {
        $_SESSION['msg_erro'] = 'Você não pode ser diretor de curso.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
switch ($tipo) {
    case 'editar':
        $sql = "SELECT * FROM curso WHERE id = {$_POST['id_curso']}";
        $res1 = my_query($sql);
        pr($_POST);
        $sql = "SELECT id FROM users WHERE users.email = '{$_POST['diretor_curso']}'";        
        $res_dc = my_query($sql);
        if(count($res_dc) == 0) {
            // não tá na plataforma
            $sql = "UPDATE curso SET ativo = 0 WHERE id = {$_POST['id_curso']}";
            my_query($sql);
            $sql = "UPDATE curso SET id_diretor_curso = -1 WHERE id = {$_POST['id_curso']}";
            my_query($sql);
            $sql = "SELECT * FROM turma WHERE id_diretor_turma = {$res1[0]['id_diretor_curso']}";
            $res = my_query($sql);
            if(count($res) > 0) {
                $sql = "UPDATE turma SET id_diretor_turma = -1 WHERE id = {$res[0]['id']}";
                my_query($sql);
            }
            $sql = "SELECT * FROM rel_turma_user WHERE id_user = {$res1[0]['id_diretor_curso']}";
            $res = my_query($sql);
            if(count($res) > 0) {
                $sql = "DELETE FROM rel_turma_user WHERE id = {$res[0]['id']}";
                my_query($sql);
            }
            $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('{$_POST['diretor_curso']}', {$_POST['id_curso']}, 'Diretor de Curso')";
            $id_inserido = my_query($sql);
            $url = $arrConfig['url_modules'] . 'trata_convite_user_plataforma_curso.mod.php?convite=' . $id_inserido;
            enviar_convite_plataforma($_POST['diretor_curso'], $url, 'Diretor de Curso', $_POST['nome_curso']);
        }        
        if($res_dc[0]['id'] == $res1[0]['id_diretor_curso']) {
            
            $sql = "UPDATE curso SET nome_curso = '{$_POST['nome_curso']}', abreviatura = '{$_POST['abreviatura']}', duracao = {$_POST['duracao']} WHERE id = {$_POST['id_curso']}";
            my_query($sql);
            header('Location: ' . $arrConfig['url_admin'] . 'instituicao.php?tab=cursos');
            exit;
        } else {            
            // tá na plataforma mas não é o atual diretor de curso
            $sql = "UPDATE curso SET ativo = 0 WHERE id = {$_POST['id_curso']}";
            my_query($sql);
            $sql = "SELECT * FROM turma WHERE id_diretor_turma = {$res1[0]['id_diretor_curso']}";
            $res = my_query($sql);
            if(count($res) > 0) {
                $sql = "UPDATE turma SET id_diretor_turma = -1 WHERE id = {$res[0]['id']}";
                my_query($sql);
            }
            $sql = "SELECT * FROM rel_turma_user WHERE id_user = {$res1[0]['id_diretor_curso']}";
            $res = my_query($sql);
            if(count($res) > 0) {
                $sql = "DELETE FROM rel_turma_user WHERE id = {$res[0]['id']}";
                my_query($sql);
            }
            $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('{$_POST['diretor_curso']}', {$_POST['id_curso']}, 'Diretor de Curso')";
            $id_inserido = my_query($sql);
            $url = $arrConfig['url_modules'] . 'trata_convite_user_curso.mod.php?convite=' . $id_inserido;
            enviar_convite_curso($_POST['diretor_curso'], $url, 'Diretor de Curso', $_POST['nome_curso']);

        }
        
        break;
    case 'criar':
        $sql = "SELECT * FROM curso 
                INNER JOIN rel_instituicao_curso ON rel_instituicao_curso.id_curso = curso.id 
                WHERE nome_curso = '{$_POST['nome_curso']}' AND rel_instituicao_curso.id_instituicao = {$_SESSION['id_instituicao']}";
        $res = my_query($sql);
        if (count($res) > 0) {
            $_SESSION['msg_erro'] = 'Curso já cadastrado nessa instituição';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        # validações associadas ao user
        $sql = "SELECT * FROM users WHERE email = '{$_POST['diretor_curso']}'";
        $res_dc = my_query($sql);
        $id_user = count($res_dc) > 0 ? $res_dc[0]['id'] : -1; // se o user não existir, o id é -1, isso vai ser usado pra validar se ele já é diretor de curso
        
        if($id_user != -1) {
            $sql = "SELECT * FROM curso WHERE id_diretor_curso = $id_user";
            $res = my_query($sql);
            if(count($res) > 0) {
                $_SESSION['msg_erro'] = 'Professor já é diretor de curso';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
        $sql = "INSERT INTO curso (nome_curso, abreviatura, duracao,id_diretor_curso, ativo) VALUES ('{$_POST['nome_curso']}', '{$_POST['abreviatura']}', {$_POST['duracao']}, -1, 0)";
        $id_curso = my_query($sql);
        $sql = "INSERT INTO rel_instituicao_curso (id_instituicao, id_curso) VALUES ({$_SESSION['id_instituicao']}, $id_curso)";
        my_query($sql);

        if(count($res_dc) == 0) { // convidar para o curso/plataforma
            $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('{$_POST['diretor_curso']}', $id_curso, 'Diretor de Curso')";
            $id_inserido = my_query($sql);
            $url = $arrConfig['url_modules'] . 'trata_convite_user_plataforma_curso.mod.php?convite=' . $id_inserido;
            enviar_convite_plataforma($_POST['diretor_curso'], $url, 'Diretor de Curso', $_POST['nome_curso']);
        } else {
            
            $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('{$_POST['diretor_curso']}', $id_curso, 'Diretor de Curso')";
            $id_inserido = my_query($sql);
            $url = $arrConfig['url_modules'] . 'trata_convite_user_curso.mod.php?convite=' . $id_inserido;
            enviar_convite_curso($_POST['diretor_curso'], $url, 'Diretor de Curso', $_POST['nome_curso']);
        }
        break;
}

header('Location: ' . $arrConfig['url_admin'] . 'instituicao.php?tab=cursos');

function convidar() {

}