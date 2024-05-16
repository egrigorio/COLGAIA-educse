<?php

include '../include/config.inc.php';
if(!isset($_SESSION['id'])){
    header('Location: ../pages/auth/login.php');
    exit;
}

?>
<?php include '../header.php'; 
if($_SESSION['cargo'] == 'instituicao') {
    $sql = "SELECT * FROM instituicao WHERE id_dono = {$_SESSION['id']}";
    $res = my_query($sql);
    
    if(count($res) > 0) {
        ?>
            <body class="bg-base-200">
                <div class="hero min-h-screen">
                    <div class="hero-content text-center">
                        <div class="max-w-md">                        
                            <div class="flex flex-col gap-8">
                                <div class="">
                                    <p class="py-3">
                                        <h1 class="text-5xl font-bold"><?php echo "Olá, " . $_SESSION['user']  ?></h1>
                                        <h3 class="py-3">bem vindo(a) de volta a educse, seu último acesso foi em: <?php echo $_SESSION['ultimo_login']; ?></h3>
                                        <div class="gap-1 flex justify-center items-center w-full">
                                            <a class="btn btn-secondary">Dashboard</a>                                        
                                            <div class="divider divider-horizontal"></div>
                                            <a href="<?php echo $arrConfig['url_admin'] . 'instituicao.php' ?>" class="btn btn-secondary">Gestão</a>
                                            <div class="divider divider-horizontal"></div>
                                            <a href="<?php echo $arrConfig['url_modules'] . 'trata_logout.mod.php' ?>" class="btn btn-secondary">Logout</a>
                                        </div>
                                    </p>
                                    
                                </div>                                     
                                <div class="">     
                                    <?php
                                    $agent = $_SERVER['HTTP_USER_AGENT'];
                                    
                                    if (strpos($agent, 'Mac') !== false) {
                                        // Code for macOS
                                        echo '<kbd class="kbd kbd-sm">⌘</kbd>';
                                    } elseif (strpos($agent, 'Windows') !== false) {
                                        // Code for Windows
                                        echo '<kbd class="kbd kbd-sm">Ctrl</kbd>';
                                    } else {
                                        // Code for other platforms
                                        echo '<kbd class="kbd kbd-sm">⌘</kbd>';
                                    }
                                    ?>                 
                                    <kbd class="kbd kbd-sm">j</kbd>
                                    <span class="text-sm">‎ para mudar de tema</span>
                                </div>
                                
                            </div>
                        </div>
                    </div> 
                </div>        
            </body>
        <?php 
    } else {
        ?>
            <div class="flex justify-center items-center mt-10">
                <form method="post" class="bg-base-100 w-4/12 border-r-8" action="<?php echo $arrConfig['url_modules'] . 'trata_editar_instituicao.mod.php?tipo=' . (isset($_GET['editar']) ? 'editar' : 'criar') ; ?>">
                    <div class="flex flex-col gap-6">
                        <h1 class="text-xl text-center font-bold">
                        <?php
                            if (isset($_GET['editar'])) {
                                $editar = true;
                                echo 'Editar';
                                $sql = "SELECT * FROM instituicao WHERE id = {$_GET['editar']}";
                                $res = my_query($sql);
                                echo '<input type="hidden" name="id_instituicao" value="' . $res['id'] . '" />';
                            } else {
                                $editar = false;
                                echo 'Criar';
                            }
                        ?> instituição</h1>
                            <input type="hidden" name="id_curso" value="<?php echo '$_GET[];'; ?>" />
                            <div class="flex flex-row gap-8">
                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">Nome da instituição</span>
                                    </div>
                                    <input name="nome" required type="text" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" value="<?php ($editar ? $res[0]['nome'] : '' ) ?>" />
                                    
                                </label>
                            </div>
                            <button class="btn w-full"><?php echo 'Criar'; ?></button>
                    </div>
                </form>
            </div>
        <?php
    }
    ?>
        
        
    <?php
} else {
    
    ?>
        <body class="bg-base-200">
            <div class="hero min-h-screen">
                <div class="hero-content text-center">
                    <div class="max-w-md">                        
                        <div class="flex flex-col gap-8">
                            <div class="">
                                <p class="py-3">
                                    <h1 class="text-5xl font-bold"><?php echo "Olá, " . $_SESSION['user']  ?></h1>
                                    <h3 class="py-3">bem vindo(a) de volta a educse, seu último acesso foi em: <?php echo $_SESSION['ultimo_login']; ?></h3>
                                    <div class="gap-1 flex justify-center items-center w-full">
                                        <a class="btn btn-secondary">Configurações</a>
                                        <div class="divider divider-horizontal"></div>
                                        <?php if($_SESSION['cargo'] == 'professor'){ ?>
                                            <a href="<?php echo $arrConfig['url_admin'] . 'turma.php' ?>" class="btn btn-secondary">Turmas</a>
                                        <?php } else { ?>
                                            <a href="<?php echo $arrConfig['url_admin'] . 'turma.php' ?>" class="btn btn-secondary">Turma</a>
                                        <?php } ?>
                                        <div class="divider divider-horizontal"></div>
                                        <a class="btn btn-secondary">Conta</a>
                                        <div class="divider divider-horizontal"></div>
                                        <a href="<?php echo $arrConfig['url_modules'] . 'trata_logout.mod.php' ?>" class="btn btn-secondary">Logout</a>
                                    </div>
                                </p>
                                
                            </div>                                     
                            <div class="">     
                                <?php
                                $agent = $_SERVER['HTTP_USER_AGENT'];                                
                                if (strpos($agent, 'Mac') !== false) {
                                    // Code for macOS
                                    echo '<kbd class="kbd kbd-sm">⌘</kbd>';
                                } elseif (strpos($agent, 'Windows') !== false) {
                                    // Code for Windows
                                    echo '<kbd class="kbd kbd-sm">Ctrl</kbd>';
                                } else {
                                    // Code for other platforms
                                    echo '<kbd class="kbd kbd-sm">⌘</kbd>';
                                }
                                ?>
                                
                                
                                    <kbd class="kbd kbd-sm">j</kbd>
                                    <span class="text-sm">‎ para mudar de tema</span>
                                
                                
                                <!-- <input type="radio" id="theme-black" name="theme-radios" class="radio bg-black theme-controller" value="black"/>
                                <input type="radio" id="theme-nord" name="theme-radios" class="radio bg-slate-300 theme-controller" value="nord"/>
                                <input type="radio" id="theme-cyberpunk" name="theme-radios" class="radio bg-yellow-300 theme-controller" value="cyberpunk"/>
                                <input type="radio" id="theme-cmyk" name="theme-radios" class="radio theme-controller" value="cmyk"/>
                                <input type="radio" id="theme-mytheme" name="theme-radios" class="radio bg-blue-400 theme-controller" value="mytheme"/>                  
                                <input type="radio" id="theme-nocas" name="theme-radios" class="radio bg-purple-400 theme-controller" value="nocas"/> -->                                                                                                
                            </div>
                            
                        </div>
                    </div>
                </div>                
            </div>        
        </body>
    <?php    

}
?>

<?php 

function getHeader($user, $ultimo_login) {
    return "
        <h1 class='text-5xl font-bold'>Olá, $user</h1>
        <h3 class='py-3'>bem vindo(a) de volta a educse, seu último acesso foi em: $ultimo_login</h3>
    ";
}



?>

</html>