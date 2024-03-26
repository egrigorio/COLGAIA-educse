<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} else if(!isset($_SESSION['codigo']) || !isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_paginas'] . 'auth/registar.php');
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
    <form action="../../modules/trata_verifconta.mod.php" method="post">
        <label for="codigo">Código que recebeu no email</label>
        <input type="number" name="codigo" id="codigo">
        <input type="submit" value="Verificar">
    </form>
    
</body>
</html>
