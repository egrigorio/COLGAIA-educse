<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} 
$_SESSION['cor'] = true;
include '../../header.php';

?>
<body>
    <form class="flex flex-col justify-center items-center h-screen" action="../../modules/trata_recuperar_senha.mod.php" method="post">
        <div class="card lg:w-6/12 shadow-xl bg-primary items-center">
            <div class="flex flex-col mt-4 mb-4 w-9/12">
                <div class="w-full text-center">
                    <div class="label">
                        <span class="label-text">Email</span>
                    </div>
                    <label class="input input-bordered flex items-center gap-2 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" /></svg>
                        <input type="text" class="grow" name="email" id="email" placeholder="Email da conta que deseja recuperar" />
                    </label>
                </div>
                <!-- <label for="email">Email da conta que deseja recuperar</label>
                <input type="email" name="email" id="email"> -->
                <input type="hidden" name="tipo" id="tipo" value="1">
                <input class="btn btn-ghost w-full" type="submit" value="Verificar">
                <?php if(isset($_SESSION['msg_sucesso'])) {
                    echo '<script>Swal.fire({icon: "success", title: "Sucesso", text: "' . $_SESSION['msg_sucesso'] . '"});</script>';
                    unset($_SESSION['msg_sucesso']);
                } ?>
            </div>
        </div>
    </form>
    
</body>
</html>
