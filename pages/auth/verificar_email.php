<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} else if(!isset($_SESSION['codigo'])){
    header('Location: ' . $arrConfig['url_paginas'] . 'auth/registar.php');
    exit;
}
$_SESSION['cor'] = true;
include '../../header.php';
?>

<body>
    <form class="flex flex-col justify-center items-center h-screen" action="../../modules/trata_verifconta.mod.php" method="post">
        <div class="flex card lg:w-6/12  shadow-xl bg-primary items-center">
        <div class="w-9/12 mt-4 mb-4 gap-4">
            <div class="w-full text-center">
                <div class="label">
                    <span class="label-text">Código de 5 digitos que recebeu no email</span>
                </div>
                <label class="input input-bordered flex items-center gap-2 w-full">
                    
                    <input type="number" min="10000" max="99999" class="grow" name="codigo" id="codigo" placeholder="12345" />
                </label>
                <input class="btn btn-ghost w-full" type="submit" value="Verificar">    
            </div>    

        </div>    
        <!-- <label for="codigo">Código que recebeu no email</label>
        <input type="number" name="codigo" id="codigo"> -->
    </form>
    
</body>
</html>

<?php
if(isset($_SESSION['erro'])){    
    echo '
    <script>
        Swal.fire({
            icon: "error",
            title: "Erro",
            text: "' . $_SESSION['erro'] . '"
        });
    </script>
    ';
    unset($_SESSION['erro']);
}
?>