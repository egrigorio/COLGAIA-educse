<?php include '../include/config.inc.php'; 
pr($_POST);

$disciplinas = $_POST['disciplinas'];

foreach($disciplinas as $disciplina) {
    // gerar abreviaturas, faz assim, se tiver espaço, então a abreviatura é as primeiras letras de cada palavra, se não, então é as duas primeiras letras 

    $abreviatura = '';
    if(strpos($disciplina, ' ') !== false) {
        $palavras = explode(' ', $disciplina);
        foreach($palavras as $palavra) {
            $abreviatura .= substr($palavra, 0, 1);
        }
    } else {
        $abreviatura = substr($disciplina, 0, 3);
    }
    $abreviatura = strtoupper($abreviatura);
    echo $abreviatura;
    
    $sql = "SELECT * FROM disciplinas WHERE nome = '$disciplina'";
    $arrResultado = my_query($sql);
    if(count($arrResultado) == 0) { 
        $sql = "INSERT INTO disciplinas (nome, abreviatura, ativo) VALUES ('$disciplina', '$abreviatura', 1)";
        $id_disciplina = my_query($sql);        
        $sql = "INSERT INTO rel_instituicao_disciplinas (id_instituicao, id_disc) VALUES ({$_SESSION['id_instituicao']}, $id_disciplina)";
        my_query($sql);        
    }


}

header('Location: ' . $_SERVER['HTTP_REFERER']);