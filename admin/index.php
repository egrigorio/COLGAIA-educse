<?php
include_once '../include/config.inc.php';

if(!isset($_SESSION['id'])){
    header('Location: ../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content text-center">
            <div class="max-w-md">
                
                <h1 class="text-5xl font-bold"><?php echo "Olá, " . $_SESSION['user']  ?></h1>
                <h3 class="py-3">bem vindo(a) de volta a educse, seu último acesso foi em: <?php echo $_SESSION['ultimo_login']; ?></h3>
                <p class="py-3">
                    <div class="gap-1 flex justify-center items-center w-full">
                        <a class="btn btn-primary">Configurações</a>
                        <div class="divider divider-horizontal"></div>                    
                        <?php if($_SESSION['cargo'] == 'professor'){ ?>
                            <a href="<?php echo $arrConfig['url_admin'] . 'turma.php' ?>" class="btn btn-primary">Turmas</a>
                        <?php } else { ?>
                            <a href="<?php echo $arrConfig['url_admin'] . 'turma.php' ?>" class="btn btn-primary">Turma</a>
                        <?php } ?>
                        <div class="divider divider-horizontal"></div>
                        <a class="btn btn-primary">Conta</a>
                        <div class="divider divider-horizontal"></div>
                        <a href="<?php echo $arrConfig['url_modules'] . 'trata_logout.mod.php' ?>" class="btn btn-primary">Logout</a>
                    </div>
                </p>
            </div>
        </div>
    </div>
</body>
</html>