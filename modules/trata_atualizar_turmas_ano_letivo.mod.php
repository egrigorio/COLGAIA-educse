<?php include '../include/config.inc.php';
if(isset($_POST['tem_turmas']) && $_POST['tem_turmas'] == 1) {
    $tem_turma = 1;
} 

$id_curso = $_SESSION['id_curso'];
$ano_letivo = get_ano_letivo();    
$sql = "SELECT abreviatura, duracao FROM curso WHERE id = $id_curso";
$res = my_query($sql);
$res = array_shift($res);

if($tem_turma) {
    $proximo_ano_letivo = get_proximo_ano_letivo($ano_letivo);            
    $abreviatura = $res['abreviatura'];
    $duracao = $res['duracao'];
    $id_user_diretor_curso = $_SESSION['id'];
    for($i = 1; $i < $duracao; $i++) {
        $nome_turma = ($i + 9) . 'º ' . $abreviatura;                                                                             // Aqui eu opero pra pegar os dados referentes a turma
        $sql = "SELECT * FROM turma WHERE nome_turma = '$nome_turma' AND id_curso = $id_curso AND ano_letivo = '$ano_letivo'";    // antiga, faço isso a partir do nome e do ano letivo da 
        $res_turma_antiga = my_query($sql);                                                                                       // turma.
        $res_turma_antiga = array_shift($res_turma_antiga);                                                                       // pego o id que assim posso remover e fazer o que quiser
        $id_turma_antiga = $res_turma_antiga['id'];                                                                               // depois. 
        
        $sql = "INSERT INTO esforco (limite) VALUES (DEFAULT)";
        $id_esforco = my_query($sql);

        $nome_nova_turma = ($i + 10) . 'º ' . $abreviatura;                                                                             // Aqui eu defino o nome da nova turma
        $sql = "INSERT INTO turma (id_curso, ano_letivo, id_esforco, nome_turma) VALUES ($id_curso, '$proximo_ano_letivo', $id_esforco ,'$nome_nova_turma')";    // Insiro a nova turma na BD
        echo $sql;        
        $res_id_turma_nova = my_query($sql);                                                                                            // e pego o id da nova turma no $res_id_turma_nova

        $sql = "SELECT * FROM rel_disciplina_curso_ano WHERE id_curso = $id_curso AND ano = $i"; // aqui eu pego as disciplinas do curso relativas ao ano para inserir na nova turma
        $res_disciplinas = my_query($sql);                                                                                                                  //
        foreach($res_disciplinas as $rel) {                                                                                                                 //
            $id_disciplina = $rel['id_disciplina'];                                                                                                         //
            $sql = "INSERT INTO rel_disciplina_turma (id_disciplina, id_turma) VALUES ($id_disciplina, $res_id_turma_nova)";                                //
            my_query($sql);                                                                                                                                 //
        }                                                                                                                                                   //        

        $sql = "INSERT INTO rel_turma_user (id_turma, id_user, ativo) VALUES ($res_id_turma_nova, $id_user_diretor_curso, 1)";
        my_query($sql);                                                                                                                 // insiro o diretor de curso na nova turma    

        $sql = "SELECT * FROM rel_turma_user 
        INNER JOIN users ON rel_turma_user.id_user = users.id                            
        WHERE users.cargo = 'professor' AND rel_turma_user.id_turma = $id_turma_antiga"; //  #####      
        $res_professores = my_query($sql);                                               //  essa query serve para separar os ids dos professores, pq eu não
                                                                                         //  quero que os professores 'avancem' de ano também por assim dizer
        
        $sql = "UPDATE rel_turma_user SET ativo = 0 WHERE id_turma = $id_turma_antiga AND id_user <> $id_user_diretor_curso"; // corto as ligações as turmas antigas por definir para 0 o ativo
        $res = my_query($sql);                                                          // 

        $sql = "SELECT * FROM rel_turma_user INNER JOIN users ON rel_turma_user.id_user = users.id WHERE users.cargo = 'aluno' AND rel_turma_user.id_turma = $id_turma_antiga"; // me certifico aqui         
        $res = my_query($sql);                                                                                                                                                  // de apenas pegar os alunos
        
        foreach($res as $rel) {
            $id_user = $rel['id_user'];             // nesse foreach, o que faço é pegar o id do user, e inserir ele na nova turma
                                                    // também tenho que atualizar as relações associadas ao turno dele, isto é, atualizar o id_turma para o id da nova turma
                                                    // no rel_user_turno, e assim, o turno dele será atualizado e a nova turma receberá os turnos da turma antiga
            $sql = "UPDATE rel_turno_user SET id_turma = $res_id_turma_nova WHERE id_user = $id_user";
            my_query($sql);
            $sql = "INSERT INTO rel_turma_user (id_turma, id_user, ativo) VALUES ((SELECT id FROM turma WHERE nome_turma = '$nome_nova_turma' AND id_curso = $id_curso AND ano_letivo = '$proximo_ano_letivo'), $id_user, 1)";                        
            $res = my_query($sql);
        }                                    
    
    } 
    $indice = 1;
    $nome_turma = ($indice + 9) . 'º ' . $abreviatura;
    $sql = "SELECT * FROM turma WHERE nome_turma = '$nome_turma' AND id_curso = $id_curso AND ano_letivo = '$ano_letivo'";
    $res_turma_antiga = my_query($sql);
    $res_turma_antiga = array_shift($res_turma_antiga);
    $id_turma_antiga = $res_turma_antiga['id'];
    $nome_nova_turma = ($indice + 9) . 'º ' . $abreviatura;
    $sql = "INSERT INTO esforco (limite) VALUES (DEFAULT)";
    $id_esforco = my_query($sql);
    $sql = "INSERT INTO turma (id_curso, ano_letivo, id_esforco ,nome_turma) VALUES ($id_curso, '$proximo_ano_letivo', $id_esforco ,'$nome_nova_turma')";
    $res = my_query($sql);
    $sql = "INSERT INTO rel_turma_user (id_turma, id_user, ativo) VALUES ($res, $id_user_diretor_curso, 1)";
    $res = my_query($sql);
    
    $sql = "SELECT * FROM rel_disciplina_curso_ano WHERE id_curso = $id_curso AND ano = $i"; // aqui eu pego as disciplinas do curso relativas ao ano para inserir na nova turma
        $res_disciplinas = my_query($sql);                                                                                                                  //
        foreach($res_disciplinas as $rel) {                                                                                                                 //
            $id_disciplina = $rel['id_disciplina'];                                                                                                         //
            $sql = "INSERT INTO rel_disciplina_turma (id_disciplina, id_turma) VALUES ($id_disciplina, $res_id_turma_nova)";                                //
            my_query($sql);                                                                                                                                 //
        }         

    // o código abaixo é para remover a última turma do curso, pois ela não será mais utilizada
    $indice = $duracao;
    $nome_turma = ($indice + 9) . 'º ' . $abreviatura;
    $sql = "SELECT * FROM turma WHERE nome_turma = '$nome_turma' AND id_curso = $id_curso AND ano_letivo = '$ano_letivo'";
    $res_turma_antiga = my_query($sql);
    $res_turma_antiga = array_shift($res_turma_antiga);
    $id_turma_antiga = $res_turma_antiga['id'];
    $sql = "UPDATE rel_turma_user SET ativo = 0 WHERE id_turma = $id_turma_antiga AND id_user <> $id_user_diretor_curso";    
    $res = my_query($sql);
    $sql = "DELETE FROM rel_turno_user WHERE id_turma = $id_turma_antiga";
    my_query($sql);

} else {
    $id_curso = $_SESSION['id_curso'];
    pr($_POST);
    $ano_letivo = $_POST['ano_letivo'];
    pr($res);
    $id_user = $_SESSION['id'];
    for($i = 1; $i <= $res['duracao']; $i++) {
        $nome_turma = $i + 9 . 'º ' . $res['abreviatura'];

        $sql = "INSERT INTO esforco (limite) VALUES (DEFAULT)";
        $id_esforco = my_query($sql);

        $sql = "INSERT INTO turma (id_curso, ano_letivo, id_esforco, nome_turma) VALUES ($id_curso, '$ano_letivo', $id_esforco, '$nome_turma')";        
        $res_id_turma_nova = my_query($sql);  
        $sql = "INSERT INTO rel_turma_user (id_turma, id_user, ativo) VALUES ($res_id_turma_nova, $id_user , 1)";
            my_query($sql);      
        
        $sql = "SELECT * FROM rel_disciplina_curso_ano WHERE id_curso = $id_curso AND ano = $i";
        $res_disciplinas = my_query($sql);
        
        /* pr($res_disciplinas); */
        foreach($res_disciplinas as $rel) {
            
            $id_disciplina = $rel['id_disciplina'];             
            $sql = "INSERT INTO rel_disciplina_turma (id_disciplina, id_turma) VALUES ($id_disciplina, $res_id_turma_nova)";        
            my_query($sql);            
        } 
    }
}

header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=turmas');
