<?php include '../include/config.inc.php'; 
pr($_POST);

$disciplinas = $_POST['disciplinas'];
$redirect = $_SERVER['HTTP_REFERER'];
$redirect = explode('?', $redirect);
$redirect = $redirect[0];

if($_POST['disciplinas'] == '') {
    $_SESSION['msg_erro'] = 'Adicione a disciplina antes de submeter.';
    header('Location: ' . $redirect . '?tab=disciplinas');
    exit();
}


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

    // Remove ou substitui caracteres não-ASCII
    $abreviatura = iconv('UTF-8', 'ASCII//TRANSLIT', $abreviatura);

    echo $abreviatura;
    
    $sql = "SELECT * FROM disciplinas WHERE nome = '$disciplina'";
    $arrResultado = my_query($sql);
    pr($arrResultado);
    

    if(count($arrResultado) == 0) { 
        $sql = "INSERT INTO disciplinas (nome, abreviatura, ativo) VALUES ('$disciplina', '$abreviatura', 1)";
        $id_disciplina = my_query($sql);        
        $sql = "INSERT INTO rel_instituicao_disciplinas (id_instituicao, id_disc) VALUES ({$_SESSION['id_instituicao']}, $id_disciplina)";
        my_query($sql);        
    } else {
        $sql = "SELECT * FROM rel_instituicao_disciplinas WHERE id_instituicao = {$_SESSION['id_instituicao']} AND id_disc = {$arrResultado[0]['id']}";
        echo $sql;
        $arrResultado2 = my_query($sql);
        if(count($arrResultado2) == 0) {
            $sql = "INSERT INTO rel_instituicao_disciplinas (id_instituicao, id_disc) VALUES ({$_SESSION['id_instituicao']}, {$arrResultado[0]['id']})";
            my_query($sql);
        }
    
    }
}


/* echo $redirect;
die; */
header('Location: ' . $redirect . '?tab=disciplinas');

