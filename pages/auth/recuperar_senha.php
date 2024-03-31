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
        <label for="password">Nova password</label>
        <input type="password" name="password" id="password">
        <label for="password2">Confirme a password</label>
        <input type="password" name="password2" id="password2">
        <input type="hidden" name="tipo" id="tipo" value="2">
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
        <input type="submit" value="Verificar">
    </form>
    
</body>
</html>
