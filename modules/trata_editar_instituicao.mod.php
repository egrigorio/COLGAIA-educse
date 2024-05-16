<?php include '../include/config.inc.php';
pr($_GET);
$editar = $_GET['tipo'];

switch ($editar) {
    case 'editar': 
        $sql = "UPDATE instituicao SET nome = {$_POST['nome']} WHERE id = {$_POST['id_instituicao']}";
        my_query($sql);
        break;
    case 'criar':
        $sql = "INSERT INTO instituicao (nome, id_dono, ativo) VALUES ('{$_POST['nome']}', {$_SESSION['id']}, 1)";
        my_query($sql);

        break;
    

}

header('Location: ' . $_SERVER['HTTP_REFERER']);