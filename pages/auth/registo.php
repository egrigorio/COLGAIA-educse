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

<?php 
$_SESSION['cor'] = true;
include '../../header.php'; 
?>
<body>
    <form class="flex flex-col justify-center items-center h-screen" action="<?php echo $action ?>" method="post">
        

        <div class="w-full lg:w-6/12 shadow-xl px-2 py-12 bg-primary">
        
            <div class="card-body flex flex-col lg:flex-row items-center">
                <div class="flex flex-col justify-center items-center  w-full lg:w-7/12 ">
                    <h2 class="card-title justify-center font-bold text-2xl">Registo</h2>            
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" /></svg>
                            <input type="text" class="grow" name="user" placeholder="Username" required />
                        </label>
                    </div>
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                            <input type="text" class="grow" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" <?php echo isset($_SESSION['email']) ? 'disabled' : '' ?> placeholder="Email" required/>
                        </label>
                    </div>
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                            <input type="password" class="grow" name="pass" placeholder="Password" required />
                        </label>
                    </div>
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                            <input type="password" class="grow" name="confirmar_pass" placeholder="Confirmar password" required />
                        </label>
                    </div>
                    <div class="w-9/12 text-center pt-5">
                        <label class="input input-bordered flex items-center gap-2">
                            <?php         
                                if(isset($_SESSION['cargo'])) {
                                    echo '<input type="text" class="grow" name="cargo" value="' . $_SESSION['cargo'] . '" disabled />';
                                } else {
                                    echo '            
                                        Cargo:
                                        <select class="bg-base-100" name="cargo" id="cargo">
                                            <option value="aluno">Aluno</option>
                                            <option value="professor">Professor</option>
                                            <option value="instituicao">Instituição</option>
                                        </select>
                                    ';
                                }
                            ?>
                        
                                
                        </label>                       
                    </div>
                    <div class="flex justify-center mt-4">
                        <button class="btn bg-base-100">Registar</button>
                    </div>
                </div>
                <div class="divider lg:divider-horizontal my-8 lg:my-0"></div>
                <div class="w-full lg:w-3/12 text-center text-5xl font-bold">
                    educse
                </div>
            </div>
            
            <div class="flex justify-center mt-4">                
            <?php 
                if(isset($_SESSION['erro'])) {
                    echo '<script>Swal.fire({icon: "error", title: "Oops...", text: "' . $_SESSION['erro'] . '"});</script>';
                    unset($_SESSION['erro']);
                }
                ?>
            </div>
            <div class="flex justify-center mt-4">                
                <span class="text-sm">Já tem conta? <a href="<?php echo $arrConfig['url_site'] . '/pages/auth/login.php' ?>"><u>Faça login.</u></a></span>
            </div>
        </div>
                                      
    </form>
    
</body>
</html>

