<?php 
include '../include/config.inc.php';
$_SESSION['cor'] = true;
include '../header.php'; 
?>
<body>
    <div class="navbar bg-base-100">
        <div class="navbar-start">        
            <a class="btn btn-ghost text-xl">educse</a>
        </div>
        <div class="navbar-end">
            <a class="btn">Login</a>
        </div>        
    </div>
    <div class="hero bg-primary min-h-80">        
        <div class="hero-content text-center text-neutral-content">
            <div class="max-w-lg">
                <h1 class="text-5xl font-bold">Manual do utilizador</h1>
                <!-- <p class="mb-5">Bem vindo ao manual de utilizador da educse. Encontre e satisfaça todas suas dúvidas relativas ao uso da aplicação. Comece por clicar
                no botão abaixo.
                </p> -->
                <!-- <button class="btn btn-base-100">Get Started</button> -->
            </div>
        </div>
    </div>
    <div id="autenticacao" class="grid grid-cols-1 lg:grid-cols-4 gap-2 p-5">    
        
    </div>
    
</body>
</html>