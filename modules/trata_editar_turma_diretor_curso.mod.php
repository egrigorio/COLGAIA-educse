<?php include '../include/config.inc.php';
pr($_POST);
$id_turmas = array();
foreach($_POST as $key => $value){
    $key = str_replace('diretor_turma_', '', $key);
    $id_turmas[$key] = $value;
}

$flag = false;

$valuesCount = array_count_values($id_turmas);
foreach($valuesCount as $value => $count) {
    if($value != -1 && $count > 1) {
        $flag = true;
        break;
    }
}

if($flag){
    $_SESSION['erro'] = 'Não é possível ter dois diretores de turma iguais';
    header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=turmas');
    die;
}

foreach($id_turmas as $key => $value){
    $sql = "UPDATE turma SET id_diretor_turma = $value WHERE id = $key";    
    my_query($sql);
}

header('Location: ' . $arrConfig['url_admin'] . 'curso.php?tab=turmas');