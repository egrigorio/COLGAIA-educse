<?php include '../include/config.inc.php';
$tem_turma = isset($_POST['tem_turma']) ? $_POST['tem_turma'] : false; 
$id_curso = $_SESSION['id_curso'];
$ano_letivo = get_ano_letivo();    
$sql = "SELECT abreviatura, duracao FROM curso WHERE id = $id_curso";
$res = my_query($sql);
$res = array_shift($res);
if($tem_turma) {
    $proximo_ano_letivo = get_proximo_ano_letivo($ano_letivo);
    
    
    $abreviatura = $res['abreviatura'];
    $duracao = $res['duracao'];
    for($i = 1; $i < $duracao; $i++) {
        $nome_turma = ($i + 9) . 'º ' . $abreviatura;
        $sql = "SELECT * FROM turma WHERE nome_turma = '$nome_turma' AND id_curso = $id_curso AND ano_letivo = '$ano_letivo'";
        $res_turma_antiga = my_query($sql);
        $res_turma_antiga = array_shift($res_turma_antiga);
        $id_turma_antiga = $res_turma_antiga['id'];
        $nome_nova_turma = ($i + 10) . 'º ' . $abreviatura;     
        $sql = "INSERT INTO turma (id_curso, ano_letivo, nome_turma) VALUES ($id_curso, '$proximo_ano_letivo', '$nome_nova_turma')";
        $res_id_turma_nova = my_query($sql);
        
        $sql = "UPDATE rel_turma_user SET ativo = 0 WHERE id_turma = $id_turma_antiga";
        $res = my_query($sql);
        $sql = "SELECT * FROM rel_turma_user WHERE id_turma = $id_turma_antiga";
        $res = my_query($sql);
        foreach($res as $rel) {
            $id_user = $rel['id_user'];
            $sql = "INSERT INTO rel_turma_user (id_turma, id_user, ativo) VALUES ((SELECT id FROM turma WHERE nome_turma = '$nome_nova_turma' AND id_curso = $id_curso AND ano_letivo = '$proximo_ano_letivo'), $id_user, 1)";
            $res = my_query($sql);
        }
        $sql = "UPDATE turno SET id_turma = $res_id_turma_nova WHERE id_turma = $id_turma_antiga";
        $res = my_query($sql);
    
    }
    $indice = 1;
    $nome_turma = ($indice + 9) . 'º ' . $abreviatura;
    $sql = "SELECT * FROM turma WHERE nome_turma = '$nome_turma' AND id_curso = $id_curso AND ano_letivo = '$ano_letivo'";
    $res_turma_antiga = my_query($sql);
    $res_turma_antiga = array_shift($res_turma_antiga);
    $id_turma_antiga = $res_turma_antiga['id'];
    $nome_nova_turma = ($indice + 9) . 'º ' . $abreviatura;
    $sql = "INSERT INTO turma (id_curso, ano_letivo, nome_turma) VALUES ($id_curso, '$proximo_ano_letivo', '$nome_nova_turma')";
    $res = my_query($sql);
} else {
    $id_curso = $_SESSION['id_curso'];
    pr($_POST);
    $ano_letivo = $_POST['ano_letivo'];
    pr($res);
    $id_user = $_SESSION['id'];
    for($i = 1; $i <= $res['duracao']; $i++) {
        $nome_turma = $i + 9 . 'º ' . $res['abreviatura'];
        
        $sql = "INSERT INTO turma (id_curso, ano_letivo, nome_turma) VALUES ($id_curso, '$ano_letivo', '$nome_turma')";        
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

header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=gestão%20das%20turmas');
