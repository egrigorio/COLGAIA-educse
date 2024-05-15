<?php
include_once '../../include/config.inc.php';
if(isset($_SESSION['id'])){
    header('Location: ' . $arrConfig['url_admin'] . 'index.php');
    exit;
}
?>

<?php include '../../header.php';

if(isset($_SESSION['id_curso'])) {
    $action = 'trata_login_convite.mod.php';
} else {
    $action = 'trata_login.mod.php';

}

?>
<body>
    <form class="flex flex-col justify-center items-center h-screen" action="<?php echo $arrConfig['url_modules'] . $action ?>" method="post">
        

        <div class="card lg:w-6/12 shadow-xl py-10 bg-primary">
        <h2 class="card-title justify-center font-bold text-2xl">Login</h2>
            <div class="card-body flex flex-col lg:flex-row items-center">
                <div class="flex flex-col justify-center items-center  w-full lg:w-7/12 ">
                    
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" /></svg>
                            <input type="text" class="grow" name="user_or_mail" placeholder="Username/Email" />
                        </label>
                    </div>                    
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                            <input type="password" class="grow" name="password" placeholder="Password" />
                            
                        </label>
                    </div>
                    <span class="justify-start text-sm mt-2"> Esqueceu sua senha? <a href="<?php echo $arrConfig['url_site'] . '/pages/auth/email_recuperar_senha.php' ?>">Recuperar.</a></span>
                    
                </div>
                <div class="divider lg:divider-horizontal my-8 lg:my-0"></div>
                <div class="w-full lg:w-3/12 text-center text-5xl font-bold">
                    educse
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <button class="btn bg-base-100">Login</button>
            </div>
            <div class="flex justify-center mt-4">                
                <?php echo isset($_SESSION['erro']) ? $_SESSION['erro'] : ''; ?>
            </div>
            <div class="flex justify-center mt-4">                
                <span class="text-sm">Ainda não tem conta? <a href="<?php echo $arrConfig['url_site'] . '/pages/auth/registo.php' ?>">Crie sua conta. </a></span> 
            </div>
            
        </div>
                                      
    </form>
    
</body>
</html>


