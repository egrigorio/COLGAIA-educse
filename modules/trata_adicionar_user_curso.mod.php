<?php
include '../include/config.inc.php';

$emails = $_POST['emails'];

$cargo = $_GET['cargo'];
$id_curso = $_GET['id_curso'];
$sql = "SELECT * FROM curso WHERE id = $id_curso";
$res_curso = my_query($sql);
$nome_curso = $res_curso[0]['nome_curso'];


$emails = array_unique($emails);
foreach($emails as $email) {    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = my_query($sql);
    $id_user = $res[0]['id'];
    if(count($res) == 0) {
                
        $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('$email', $id_curso, '$cargo')";
        $id_inserido = my_query($sql);        
        $url = $arrConfig['url_modules'] . 'trata_convite_user_plataforma_curso.mod.php?convite=' . $id_inserido;                
        enviar_convite_plataforma($email, $url, $cargo, $nome_curso);
        

    } else {
        //validar cargo do convite com cargo do user
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $res = my_query($sql);
        if($cargo == $res[0]['cargo']) {
            $sql = "SELECT * FROM rel_user_curso WHERE id_user = $id_user AND id_curso = $id_curso";
            $res = my_query($sql);
            if(count($res) == 0) {
                $sql = "INSERT INTO rel_user_curso (id_user, id_curso, cargo, estado) VALUES ($id_user, $id_curso, '$cargo', 'Convite enviado')";
                my_query($sql);
                $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('$email', $id_curso, '$cargo')";
                $id_inserido = my_query($sql);            
                $url = $arrConfig['url_modules'] . 'trata_convite_user_curso.mod.php?convite=' . $id_inserido;
                enviar_convite_curso($email, $url, $cargo, $nome_curso);
            } else {
                // tratar exceção de o user já estar na turma
            }
        } else {
            // não faz inserção pois o user não tem o cargo correto
        }

    }

}

header('Location:' . $arrConfig['url_admin'] . 'curso.php?');
