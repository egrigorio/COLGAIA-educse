<?php 

include '../include/config.inc.php';
include 'dashboards/layout.dash.php';
if(!isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'default'; ?>" class="bg-primary" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../public/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php

if(isset($_GET['id_turma']) && $_GET['id_turma']) {

    $id_turma = $_GET['id_turma'];

    /* validar se a turma existe */
    $sql = "SELECT * FROM turma WHERE id = $id_turma";
    $arrResultado = my_query($sql);
    if (count($arrResultado) == 0) {
        echo '<h1>turma não existe</h1>';
        $_SESSION['erro'] = 'Turma não existe';
        /* header('Location: ' . $arrConfig['url_admin'] . 'index.php'); */
        exit;
    }
    $turma = $arrResultado[0];

    /* validar se quem acessa essa página pertence a turma */
    $sql = "SELECT * FROM rel_turma_user WHERE id_turma = $id_turma AND id_user = {$_SESSION['id']}";
    $arrResultado = my_query($sql);
    if (count($arrResultado) == 0) {
        echo '<h1>não pertence a turma</h1>';
        $_SESSION['erro'] = 'Não pertence a turma';
        /* header('Location: ' . $arrConfig['url_admin'] . 'index.php'); */
        exit;
    }
        
} else {
    
}


$cargo = $_SESSION['cargo'];

switch($cargo) {
    case 'aluno':
        include './dashboards/aluno.dash.php';
        break;
    case 'professor':
        include './dashboards/professor.dash.php';
        break;
}
?>




</body>
</html>


