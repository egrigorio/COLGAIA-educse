<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../../modules/trata_registo.mod.php" method="post">
        <label for="user">Utilizador</label>
        <input type="text" name="user" id="user">
        <label for="email">Email</label>
        <input type="email" name="email" id="email">        
        <label for="pass">Password</label>
        <input type="password" name="pass" id="pass">
        <label for="confirmar_pass">Confirmar Password</label>
        <input type="password" name="confirmar_pass" id="confirmar_pass">
        <label for="cargo">Cargo</label>
        <select name="cargo" id="cargo">
            <option value="aluno">Aluno</option>
            <option value="professor">Professor</option>
        <input type="submit" value="Registo">
    </form>
    <?php echo $_SESSION['erro']; ?>
</body>
</html>

