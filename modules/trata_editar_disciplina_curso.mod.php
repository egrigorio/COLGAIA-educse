<?php include '../include/config.inc.php';


$id_curso = $_SESSION['id_curso'];
$arr_disciplinas = array();
foreach($_POST as $k => $v) {
    $parts = explode('%', $k);
    $id_disciplina = $parts[0];
    $ano_disciplina = $parts[1];
    $arr_disciplinas[$id_disciplina][$ano_disciplina] = $v;
    

}



foreach($arr_disciplinas as $k => $v){
    $sql = "DELETE FROM rel_disciplina_curso_ano WHERE id_disciplina = $k AND id_curso = $id_curso";
    my_query($sql);
    
    $isInAnyYear = false;
    foreach($v as $k2 => $v2) {
        if ($v2 == 'on') {
            $isInAnyYear = true;
            break;
        }
    }

    if ($isInAnyYear) {
        foreach($v as $k2 => $v2) {
            if ($v2 == 'on') {
                $sql = "INSERT INTO rel_disciplina_curso_ano (id_disciplina, id_curso, ano) VALUES ($k, $id_curso, $k2)";
                my_query($sql);
            }
        }
    } 
}
pr($_POST);
pr($arr_disciplinas);


header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=disciplinas');