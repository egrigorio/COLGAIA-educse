<?php 
include '../include/config.inc.php';
/* include 'dashboards/layout.dash.php'; */
if(!isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}
?>
<?php include '../header.php';?>
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
    $sql = "SELECT * FROM rel_turma_user WHERE id_turma = $id_turma AND id_user = {$_SESSION['id']} AND ativo = 1";
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
if(isset($_SESSION['convite_aceite']) && $_SESSION['convite_aceite']) {
    echo '
    <script>
    Swal.fire({
        title: "Convite aceite com sucesso!",
        text: "Você já pertence ao curso!",
        icon: "success"
      });
    </script>';
    unset($_SESSION['convite_aceite']);
}

switch(strtolower($cargo)) {
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


