<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
} 
?>

<?php 
$_SESSION['cor'] = true;
include '../../header.php'; ?>
<body>
    <form class="flex flex-col justify-center items-center h-screen" action="../../modules/trata_recuperar_senha.mod.php" method="post">
        <div class="flex card lg:w-6/12 shadow-xl bg-primary items-center">
            <div class="w-9/12 mt-4 mb-4 gap-4">
                <div class="w-full text-center">
                        <div class="label">
                            <span class="label-text">Nova password</span>
                        </div>
                        <label class="input input-bordered flex items-center gap-2 w-full">                        
                            <input type="password" class="grow" name="password" id="password" required placeholder="*********" />
                        </label>
                    
                </div>
                <div class="w-full text-center">
                        <div class="label">
                            <span class="label-text">Confirme a password</span>
                        </div>
                        <label class="input input-bordered flex items-center gap-2 w-full">
                            
                            <input type="password" class="grow" name="password2" id="password2" required placeholder="*********" />
                        </label>
                    
                </div>
                <input type="hidden" name="tipo" id="tipo" value="2">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">             
                <input class="btn btn-ghost w-full" type="submit" value="Verificar">
            </div>
        </div>
        <span class="text mt-10">Voltar para o <u><a href="<?php echo $arrConfig['url_site'] . '/pages/auth/login.php' ?>">login</a></u></span>
        <?php if(isset($_SESSION['msg_sucesso'])) {
            echo '<script>Swal.fire({icon: "success", title: "Sucesso", text: "' . $_SESSION['msg_sucesso'] . '"});</script>';
            unset($_SESSION['msg_sucesso']);
            
        } ?>
        <!-- <label for="password">Nova password</label>
        <input type="password" name="password" id="password">
        <label for="password2">Confirme a password</label>
        <input type="password" name="password2" id="password2"> -->
        
    </form>
    
</body>
</html>

<?php 
if(isset($_SESSION['erro'])) {
    echo '<script>Swal.fire({icon: "error", title: "Erro", text: "' . $_SESSION['erro'] . '"});</script>';
    unset($_SESSION['erro']);
}
?>