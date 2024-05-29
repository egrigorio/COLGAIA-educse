<?php
include '../include/config.inc.php';

pr($_FILES);

$emails = array();
if(isset($_POST['emails']) && $_POST['emails']){
    $emails = $_POST['emails'];
}
if(isset($_FILES['csv']) && $_FILES['csv']['error'] == 0){
    
    $name = $_FILES['csv']['name'];
    $type = $_FILES['csv']['type'];
    $tmpName = $_FILES['csv']['tmp_name'];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            
            //set_time_limit(0);

            $row = 0;

            //$header = fgetcsv($handle, 1000, ';');

            //pr($header);
            
            while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                
                if (is_array($data) && count($data) <= 1) {                                        
                    pr($data);
                    if(filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                        $emails[] = trim($data[0]);
                    }
                    $row++;
                }
            }
            fclose($handle);
        }
    } else {
        echo "O arquivo deve ser um CSV.";
    }
}

$id_curso = $_GET['id_curso'];

$sql = "SELECT email FROM conf_convite WHERE id_curso = $id_curso";
$res = my_query($sql); // me devolve os emails que já foram convidados

foreach($res as $r) {
    if(!in_array($r['email'], $emails)) { // 
        $emails_separados[] = $r['email'];
    }
}

foreach($res as $r) {
    if(in_array($r['email'], $emails)) {        
        $key = array_search($r['email'], $emails);
        unset($emails[$key]);
    }
}

$cargo = $_GET['cargo'];
$sql = "SELECT * FROM curso WHERE id = $id_curso";
$res_curso = my_query($sql);
$nome_curso = $res_curso[0]['nome_curso'];


$emails = array_unique($emails);
foreach($emails as $email) {    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = my_query($sql);
    if(count($res) == 0) {
                        
        $sql = "INSERT INTO conf_convite (email, id_curso, cargo) VALUES ('$email', $id_curso, '$cargo')";
        echo $sql;
        $id_inserido = my_query($sql);        
        $url = $arrConfig['url_modules'] . 'trata_convite_user_plataforma_curso.mod.php?convite=' . $id_inserido;                
        enviar_convite_plataforma($email, $url, $cargo, $nome_curso);
        

    } else {
        $id_user = $res[0]['id'];
        //validar cargo do convite com cargo do user        
        if(strtolower($cargo) == strtolower($res[0]['cargo'])) {
            $sql = "SELECT * FROM rel_user_curso WHERE id_user = $id_user AND id_curso = $id_curso";
            echo $sql;
            $res = my_query($sql);
            if(count($res) == 0) {
                $sql = "INSERT INTO rel_user_curso (id_user, id_curso, estado) VALUES ($id_user, $id_curso, 'Convite enviado')";
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

function def_redirect($cargo) {
    if($cargo == 'aluno') {
        return 'curso.php?tab=alunos';
    } else {
        return 'curso.php?tab=professores';
    }
}

$redirect = def_redirect($cargo);
echo $redirect;


header('Location:' . $arrConfig['url_admin'] . $redirect);
