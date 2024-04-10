<?php
include '../../include/config.inc.php';

$id_evento = $_GET['id_evento'];
$sql = "SELECT eventos.*, atividades.* FROM eventos 
INNER JOIN atividades ON eventos.id = atividades.id_evento AND atividades.id_professor = {$_SESSION['id']} 
WHERE eventos.id = $id_evento";
$res = my_query($sql);



if(count($res) !== 0) { /* garantir que o user que esotu alterando informações é do curso, para evitar situações em que alguém altere dados do parametro */

    /* quero permitir mudar: disciplinas, turmas, direção de turmas */
    ?>

    <!DOCTYPE html>
    <html data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'default'; ?>" class="bg-base-200" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="../../public/styles.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/select2_override.css' ?>">
    </head>
    <body class="h-screen">
        
        <form method="post" class="mt-10" action="<?php echo $arrConfig['url_modules'] . 'trata_editar_user_curso.mod.php' ?>">
            <input type="hidden" name="id_user" value="<?php echo $id_user ?>">
            <div class="flex justify-around">
                <div class="card w-8/12 bg-primary shadow-xl">
                    <div class="card-body items-center text-center">
                        <h1 class="flex text-xl font-bold mb-5">Edição do user no curso</h1>
                        <div class="flex gap-14">
                            <div class="">
                                
                                <div>
                                    <span>Título do evento</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="text" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span>Começo</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="text" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span>Fim</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="select" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="flex-row">
                                <div>
                                    <span>Título do evento</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="text" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span>Começo</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="text" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <span>Fim</span>
                                    <div class="w-auto text-center pt-5">
                                        <label class="input input-bordered flex items-center gap-2">
                                            <input type="select" class="grow" placeholder="Search" />
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            
                        </div>   
                        <button class="btn btn-ghost" type="submit">Enviar</button>          
                    </div>
                </div>
            </div>
            
        </form>
    </body>
    </html>

    <?php
    

}

