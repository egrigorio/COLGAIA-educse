<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}

if(isset($_SESSION['id_curso'])) {
    $action = $arrConfig['url_modules'] . 'trata_registo_convite.mod.php';
} else {
    $action = $arrConfig['url_modules'] . 'trata_registo.mod.php';

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
    <form action="<?php echo $action ?>" method="post">
        <label for="user">Utilizador</label>
        <input type="text" name="user" id="user">
        <label for="email">Email</label>
        <input type="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" <?php echo isset($_SESSION['email']) ? 'disabled' : '' ?> name="email" id="email">        
        <label for="pass">Password</label>
        <input type="password" name="pass" id="pass">
        <label for="confirmar_pass">Confirmar Password</label>
        <input type="password" name="confirmar_pass" id="confirmar_pass">
        <label for="cargo">Cargo</label>
        <?php         
        if(isset($_SESSION['cargo'])) {
            echo '<input type="text" value="' . $_SESSION['cargo'] . '" disabled>';
        } else {
            echo '            
                <select name="cargo" id="cargo">
                    <option value="aluno">Aluno</option>
                    <option value="professor">Professor</option>
                </select>
            ';
        }
        ?>
        <input type="submit" value="Registo">
    </form>
    <?php echo $_SESSION['erro']; ?>
</body>
</html>

