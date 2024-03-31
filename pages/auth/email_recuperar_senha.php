<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../../modules/trata_recuperar_senha.mod.php" method="post">
        <label for="email">Email da conta que deseja recuperar</label>
        <input type="email" name="email" id="email">
        <input type="hidden" name="tipo" id="tipo" value="1">
        <input type="submit" value="Verificar">
    </form>
    
</body>
</html>
