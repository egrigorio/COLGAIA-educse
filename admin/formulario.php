<?php
/* .../formulario.php?tipo=inserir&modulo=curso& */
include '../include/config.inc.php';
include_once $arrConfig['dir_include'] . 'auth.inc.php';
if(!isset($_SESSION['cargo']) && $_SESSION['cargo'] != 'aluno') {
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}
if(!isset($_GET['tipo']) || !isset($_GET['modulo'])) {
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}
$tipo = $_GET['tipo'];
$modulo = $_GET['modulo'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário</title>
</head>
<body>
    <?php 
    
    switch($tipo) {
        case 'inserir':
            gerar_formulario($tipo, $modulo);
            break;
        case 'editar':
            gerar_formulario($tipo, $modulo, 'id', $_GET['id']);
            break;        
    }

    ?>
</body>
</html>