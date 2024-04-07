<?php
include '../../include/config.inc.php';

$id_user = $_GET['id_user'];

$sql = "SELECT * FROM rel_user_curso WHERE id_user = $id_user";
$res = my_query($sql);
$arr_disciplinas = buscar_disciplinas_cargo($id_user, 'professor', $_SESSION['id_curso']);
$arr_nome_disciplinas = array();
foreach($arr_disciplinas as $disciplina) {
    $sql = "SELECT * FROM disciplinas WHERE id = " . $disciplina['id_disciplina'];
    $res = my_query($sql);
    $arr_nome_disciplinas = array_merge($arr_nome_disciplinas, $res);
}
$arr_turmas = buscar_turmas_participa_curso($id_user, $_SESSION['id_curso']);


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
                                <span>Disciplinas</span>
                                <?php gerar_campos_de_edicao('disciplinas', $arr_nome_disciplinas); ?>
                            </div>
                            <div>
                                <span>Turmas</span>
                                <?php gerar_campos_de_edicao('turmas', $arr_turmas); ?>
                            </div>
                            <div>
                                <span>Direção de turma</span>
                                <div class="w-auto text-center pt-5">
                                    <label class="input input-bordered flex items-center gap-2">
                                        <input type="select" class="grow" placeholder="Search" />
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                                    </label>
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


function gerar_campos_de_edicao($campo, $arrDados) {
    global $arrConfig;
    

    switch($campo) {
        case 'disciplinas': 
            $arr_disciplinas = buscar_disciplinas_curso($_SESSION['id_curso'], 0);
            
            gerar_input_dinamico('Disciplina', 'nome', 'id', $arrDados, 'select', $arr_disciplinas, 'id', 'nome');
            break;
        case 'turmas':
            $arr_turmas = buscar_turmas_curso($_SESSION['id_curso']);
            gerar_input_dinamico('Turma', 'nome_turma', 'id', $arrDados, 'select', $arr_turmas, 'id', 'nome_turma');
            break;
    }
            
}

function gerar_input_dinamico($campo, $nome, $chave, $arrDados, $tipo_input, $arr_opcoes_input = null, $chave_opcoes_input = null, $nome_opcoes_input = null) {
    global $arrConfig;
    $valores_ja_inseridos = array();
    echo '
    
    <div class="w-auto text-center pt-5">
        
        
            <div class="flex mb-4 gap-2">
                <label class="input input-bordered flex items-center gap-2">                    
                    ';
    switch($tipo_input) {
        case 'text': 
            echo '<input type="text" class="grow" id="' . $campo . '-input" placeholder="' . $campo . '" />';
            break;
        case 'select':
            echo '<select id="' . $campo . '-input" class="grow" onchange="document.getElementById(\'' . $campo . '-input-text\').value=this.options[this.selectedIndex].text">';
            echo '<option value="">Selecione uma opção</option>';
            foreach($arr_opcoes_input as $opcao) {                
                echo '<option value="' . $opcao[$chave_opcoes_input] . '">' . $opcao[$nome_opcoes_input] . '</option>';
            }            
            echo '</select>';
            echo '<input type="hidden" id="' . $campo . '-input-text" value="">'; 
            break;
    }                    
    echo '
                </label>
                <button type="button" id="add-' . $campo . '" class="btn btn-ghost text-xs py-1 px-2">
                    Adicionar
                </button>
                
            </div>
            <div id="' . $campo . '-list" class="mb-4">
                ';
                foreach($arrDados as $dado) {
                    $valores_ja_inseridos[] = $dado[$chave];
                    echo '
                        <div class="flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs">
                            <span class="flex-auto">' . $dado[$nome] . '</span>
                            <button type="button" onclick="remove' . $campo . '(this)" class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Remover</button>
                            <input type="hidden" name="' . $campo . '[]" value="' . $dado[$chave] . '">
                        </div>

                    ';
                    
                }
                
                
    echo '
            </div>
        
    </div>
    

        <script>
            document.addEventListener(\'DOMContentLoaded\', function() {
                $(\'#' . $campo . '-input\').select2();

            });
            var insertedValues' . $campo . ' = ["' . implode('","', array_map('addslashes', $valores_ja_inseridos)) . '"];
            console.log(insertedValues' . $campo . ');
            document.getElementById("' . $campo . '-input").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); 
                    document.getElementById("add-' . $campo . '").click();
                }
            });
    
            ';

            switch ($tipo_input) {
                case 'text': 
                    echo '
                    
                    document.getElementById("add-' . $campo . '").onclick = function() {
                        var ' . $campo . 'Input = document.getElementById("' . $campo . '-input");
                        var ' . $campo . 'List = document.getElementById("' . $campo . '-list");
                        var ' . $campo . ' = ' . $campo . 'Input.value.trim();
                                            
                        if(' . $campo . ') {
                            
                            var newDiv = document.createElement("div");
                            newDiv.className = "flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs";
                            
                            var newInput = document.createElement("input");
                            newInput.type = "hidden";
                            newInput.name = "' . $campo . '[]";
                            newInput.value = ' . $campo . ';
                            
                            newDiv.innerHTML = `<span class="flex-auto">${' . $campo . '}</span>
                                        <button type="button"
                                                onclick="remove' . $campo . '(this)"
                                                class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                            Remover
                                        </button>`;
                            newDiv.appendChild(newInput);                        
                            ' . $campo . 'List.appendChild(newDiv);                        
                            ' . $campo . 'Input.value = "";
                        }
                    };
                    
                    ';
                    break;
                case 'select': 
                    echo '
                    
                    document.getElementById("add-' . $campo . '").onclick = function() {
                        var ' . $campo . 'Input = document.getElementById("' . $campo . '-input");
                        var ' . $campo . 'List = document.getElementById("' . $campo . '-list");
                        var ' . $campo . ' = ' . $campo . 'Input.value.trim();

                        if(' . $campo . ') {
                            console.log(insertedValues' . $campo . ');                  
                            if (insertedValues' . $campo . '.includes(' . $campo . ')) {
                                Swal.fire({
                                    title: "' . $campo . ' repetida",
                                    text: "' . $campo . ' já foi inserida anteriormente",
                                    icon: "error",
                                    timer: 2000,
                                    
                                })
                                return;
                            }
                            
                            var newDiv = document.createElement("div");
                            newDiv.className = "flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs";
                            
                            var newInput = document.createElement("input");
                            newInput.type = "hidden";
                            newInput.name = "' . $campo . '[]";
                            newInput.value = ' . $campo . ';
                            
                            var ' . $campo . 'Text = document.getElementById("' . $campo . '-input-text").value; // Adicionado aqui
                            newDiv.innerHTML = `<span class="flex-auto">${' . $campo . 'Text}</span>
                                        <button type="button"
                                                onclick="remove' . $campo . '(this)"
                                                class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                            Remover
                                        </button>`; // Adicionado aqui
                            newDiv.appendChild(newInput);                        
                            ' . $campo . 'List.appendChild(newDiv);                        
                            ' . $campo . 'Input.value = "";

                            insertedValues' . $campo . '.push(' . $campo . ');
                        }
                    };

                    ';
                    break;
            }

            echo '
    
            function remove' . $campo . '(button) {        
                button.parentElement.remove();
                var index = insertedValues' . $campo . '.indexOf(button.parentElement.querySelector("input").value);
                if (index > -1) {
                    insertedValues' . $campo . '.splice(index, 1);
                }
            }
        </script>

    ';

    
}