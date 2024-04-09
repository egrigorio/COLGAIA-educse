<?php
include '../include/config.inc.php';
include_once 'configuracoes.adm.php';
include_once 'gerar_calendario_atividades.php';
function navbar($arr_items) {
    global $arrConfig;
    $teste = parse_url($_SERVER['REQUEST_URI']);
    $query = isset($teste['query']) ? $teste['query'] : '';

    echo '
    
    <div class="navbar bg-base-100">
        <div class="navbar-start">
            <a href="' . $arrConfig['url_admin'] . 'index.php' . '" class="btn btn-ghost text-2xl">educse</a>
        </div>
        <div class="navbar-center">
            
        ';

    foreach ($arr_items as $item) {
        if (isset($item['nome_curso'])) {
            if ($query == '') {
                echo '
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?" class="btn btn-ghost text-sm btn-active">' . $item['abreviatura'] . '</a>
                    ';
            } else {
                echo '
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?" class="btn btn-ghost text-sm">' . $item['abreviatura'] . '</a>
                    ';
            }
        } else {
            if ($query == 'id_turma=' . $item['id']) {
                echo '
                <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $item['id'] . '" class="btn btn-ghost text-sm btn-active">' . $item['nome_turma'] . '</a>
                ';
            } else {
                echo '
                <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $item['id'] . '" class="btn btn-ghost text-sm">' . $item['nome_turma'] . '</a>
                ';
            }
        }
    }

    echo '
            
        </div>
        <div class="navbar-end">                        
            <button class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div>
            </button>

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Tailwind CSS Navbar component" src="' . $arrConfig['url_pfp'] . $_SESSION['pfp'] . '" />
                    </div>
                </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                        </li>
                        <li><a>Settings</a></li>
                        <li><a href="' . $arrConfig['url_modules'] . 'trata_logout.mod.php' . '">Logout</a></li>
                    </ul>
                    
                </div>
            </div>
    </div>
    
    ';
}

function turma($arr_turma, &$flag_direcao_turma) {

    if ($arr_turma[0]['id_diretor_turma'] == $_SESSION['id']) {
        $flag_direcao_turma = true;
    }
    echo '
    
    <div class="bg-primary flex justify-center w-full h-52 text-center">
        <b>
            <h1 class="bold mt-20 text-4xl">' . $arr_turma[0]['nome_turma'] . '</h1>';
    echo $flag_direcao_turma ? '<span class="flex items-center justify-center">diretor de turma</span>' : '';
    echo '
        </b>
    </div>
    <div role="tablist" class="bg-primary tabs tabs-lifted">

    ';
    $tabs = $flag_direcao_turma ? 'tabs_direcao_turma' : 'tabs_turma';
    def_config_adm($tabs, $arr_config);
    foreach ($arr_config as $kCampos => $vCampos) {
        echo '
        <input type="radio" name="' . $vCampos['name'] . '" role="tab" class="tab" aria-label="' . $vCampos['label'] . '" ' . ($vCampos['checked'] == 1 ? 'checked' : '') . ' />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
            ' . $vCampos['content'] . '
        </div>
        ';
    }
    echo '
    </div>
    ';

}

function curso() {
    $sql = "SELECT * FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
    $res = my_query($sql);
    $arr_config = array();
    $_SESSION['id_curso']  = $res[0]['id'];
    $sql = "SELECT id_instituicao FROM rel_instituicao_curso WHERE id_curso = " . $_SESSION['id_curso'];
    $res2 = my_query($sql);
    $id_instituicao = $res2[0]['id_instituicao'];
    $_SESSION['id_instituicao'] = $id_instituicao;

    echo '
    
    <div class="bg-primary flex justify-center w-full h-52 ">
        <b>
            <h1 class="bold mt-20 text-4xl">' . $res[0]['nome_curso'] . '</h1> 
            <span class="flex items-center justify-center">' . $res[0]['abreviatura'] . '</span>   
        </b>
    </div>
    <div role="tablist" class="bg-primary tabs tabs-lifted">
    ';
    def_config_adm('tabs_curso', $arr_config);
    foreach ($arr_config as $kCampos => $vCampos) {
        echo '
        <input type="radio" name="' . $vCampos['name'] . '" role="tab" class="tab" aria-label="' . $vCampos['label'] . '" ' . ($vCampos['checked'] == 1 ? 'checked' : '') . ' />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
        ' . $vCampos['content'] . '
        </div>
        ';
    }
    echo '
    </div>
    ';

}

function professores_tabs_cursos() {
    global $arrConfig;    
    $id_curso = $_SESSION['id_curso'];

    $html = '
    
    <div class="flex justify-around">
        <div class="w-auto text-center pt-5">
            <h2 class=" text-lg mb-4 ">Adicionar professores ao curso</h2>
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_user_curso.mod.php?cargo=professor&id_curso=' . $id_curso . '" id="emailForm">
                <div class="flex mb-4 gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                        <input type="text" class="grow" id="email-input" placeholder="Email" />
                    </label>
                    <button type="button" id="add-email" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button type="submit" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                </div>
                <div id="email-list" class="mb-4">
                    <!-- Os itens de e-mail serão inseridos aqui -->
                </div>
            </form>
        </div>
        <div class="divider lg:divider-horizontal"></div> 
        <div class="">
            <label class="form-control w-full max-w-xs mb-5">
                <div class="label">
                    <span class="label-text">Escolha a visualização</span>                    
                </div>
                <select class="select select-bordered" id="select_tabelas">                    
                    <option value="convidados">Convidados</option>
                    <option value="efetivos" selected>Efetivos</option>
                </select>
                
            </label>
            <div class="overflow-x-auto">
                <table class="table" id="tabela_professores">                                                                
                
                </table>                        
            </div>
            
        </div>
    </div>
        <script>
            document.getElementById(\'select_tabelas\').addEventListener(\'change\', function() {
                console.log(this.value);
                var valorSelecionado = this.value;
                var tabela = document.getElementById(\'tabela_professores\');

                tabela.innerHTML = "";
                var xhr = new XMLHttpRequest();
                    xhr.open(\'GET\', \'' . $arrConfig['url_admin'] . 'dashboards/' . 'gerar_tabelas_views_professores.php?valor=\' + valorSelecionado, true);
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            
                            tabela.innerHTML = this.responseText;
                        }
                    };
                    xhr.send();
            });

            var event = new Event(\'change\');
            var select = document.getElementById(\'select_tabelas\');
            select.value = \'efetivos\';
            select.dispatchEvent(event);

            document.getElementById("email-input").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); 
                    document.getElementById("add-email").click();
                }
            });
    
            document.getElementById("add-email").onclick = function() {
                var emailInput = document.getElementById("email-input");
                var emailList = document.getElementById("email-list");
                var email = emailInput.value.trim();
                
                var re = /\S+@\S+\.\S+/;
                if(!re.test(email)) {
                    Swal.fire({
                        title: "Email inválido",
                        text: "O email deve ser no formato \'email@example.com\'",
                        icon: "error",
                        timer: 2000,
                        
                    })
                    return;
                }
    
                if(email) {
                    var newDiv = document.createElement("div");
                    newDiv.className = "flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs";
                    
                    var newInput = document.createElement("input");
                    newInput.type = "hidden";
                    newInput.name = "emails[]";
                    newInput.value = email;
                    
                    newDiv.innerHTML = `<span class="flex-auto">${email}</span>
                                <button type="button"
                                        onclick="removeEmail(this)"
                                        class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                    Remover
                                </button>`;
                    newDiv.appendChild(newInput);                        
                    emailList.appendChild(newDiv);                        
                    emailInput.value = "";
                }
            };
    
            function removeEmail(button) {        
                button.parentElement.remove();
            }
        </script>
    
        
        ';

    return $html;
}

function alunos_tabs_cursos() {

    global $arrConfig;
    $id_curso = $_SESSION['id_curso'];  

    $html = '
    
    <div class="flex justify-around">
        <div class="w-auto text-center pt-5">
            <h2 class=" text-lg mb-4 ">Adicionar alunos ao curso</h2>
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_user_curso.mod.php?cargo=aluno&id_curso=' . $id_curso . '" id="disciplinas_form">
                <div class="flex mb-4 gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                        <input type="text" class="grow" id="email-input_alunos" placeholder="Email" />
                    </label>
                    <button type="button" id="add-email_alunos" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button type="submit" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                </div>
                <div id="email-list_alunos" class="mb-4">
                    <!-- Os itens de e-mail serão inseridos aqui -->
                    
                </div>
            </form>
        </div>
        <div class="divider lg:divider-horizontal">
        </div> 
        <div class="">
            <label class="form-control w-full max-w-xs mb-5">
                <div class="label">
                    <span class="label-text">Escolha a visualização</span>                    
                </div>
                <select class="select select-bordered" id="select_tabelas_alunos">                    
                    <option value="convidados">Convidados</option>
                    <option value="efetivos" selected>Efetivos</option>
                </select>
                
            </label>
            <div class="overflow-x-auto">
                <table class="table" id="tabela_alunos">                                                                
                
                </table>                        
            </div>            
        </div>
        
    </div>
    

    <script>
        document.getElementById(\'select_tabelas_alunos\').addEventListener(\'change\', function() {
            
            var valorSelecionado = this.value;
            var tabela = document.getElementById(\'tabela_alunos\');

            tabela.innerHTML = "";
            var xhr = new XMLHttpRequest();
                xhr.open(\'GET\', \'' . $arrConfig['url_admin'] . 'dashboards/' . 'gerar_tabelas_views_alunos.php?valor=\' + valorSelecionado, true);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        
                        tabela.innerHTML = this.responseText;
                    }
                };
                xhr.send();
        }); 

        var event = new Event(\'change\');
        var select = document.getElementById(\'select_tabelas_alunos\');
        select.value = \'efetivos\';
        select.dispatchEvent(event);
       
        document.getElementById("email-input_alunos").addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); 
                document.getElementById("add-email_alunos").click();
            }
        });

        document.getElementById("add-email_alunos").onclick = function() {
            var emailInput = document.getElementById("email-input_alunos");
            var emailList = document.getElementById("email-list_alunos");
            var email = emailInput.value.trim();
            
            var re = /\S+@\S+\.\S+/;
            if(!re.test(email)) {
                Swal.fire({
                    title: "Email inválido",
                    text: "O email deve ser no formato \'email@example.com\'",
                    icon: "error",
                    timer: 2000,
                    
                })
                return;
            }

            if(email) {
                var newDiv = document.createElement("div");
                newDiv.className = "flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs";
                
                var newInput = document.createElement("input");
                newInput.type = "hidden";
                newInput.name = "emails[]";
                newInput.value = email;
                
                newDiv.innerHTML = `<span class="flex-auto">${email}</span>
                            <button type="button"
                                    onclick="removeEmail(this)"
                                    class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                Remover
                            </button>`;
                newDiv.appendChild(newInput);                        
                emailList.appendChild(newDiv);                        
                emailInput.value = "";
            }
        };

        function removeEmail(button) {        
            button.parentElement.remove();
        }
    </script>

    ';

    return $html;

}

function disciplinas_tabs_cursos() {

    global $arrConfig;
    $id_curso = $_SESSION['id_curso'];  

    $sql = "SELECT rel_instituicao_disciplinas.id_disc, disciplinas.abreviatura, disciplinas.nome 
    FROM rel_instituicao_disciplinas 
    INNER JOIN disciplinas 
    WHERE rel_instituicao_disciplinas.id_disc = disciplinas.id AND rel_instituicao_disciplinas.id_instituicao = " . $_SESSION['id_instituicao'] . " AND
    rel_instituicao_disciplinas.id_disc NOT IN 
    (SELECT id_disciplina FROM rel_disciplina_curso WHERE id_curso = " . $id_curso . ")";
    $arr_disciplinas = my_query($sql);    

    $html = '
    
    <div class="flex justify-around">
        <div class="w-auto text-center pt-5">
            <h2 class=" text-lg mb-4 ">Adicionar Disciplinas ao curso</h2>
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_disciplina_curso.mod.php?id_curso=' . $id_curso . '" id="disciplinas_form">
                <div class="flex mb-4 gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <i class="fa fa-book"></i>
                        <select class="select select-bordered w-full max-w-xs" id="disciplinas-input">
                            <option value="">Escolha a disciplina</option>
                            ';
                            
    foreach($arr_disciplinas as $disciplina) {

        $html .= '<option value="' . $disciplina['id_disc'] . '">' . $disciplina['abreviatura'] . ' - ' . $disciplina['nome'] . '</option>';
    }

    $html .= '
                            <!-- Adicione mais opções conforme necessário -->
                        </select>
                        </label>                        
                    <button type="button" id="add-disciplinas" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button type="submit" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                </div>
                <div id="disciplinas-list" class="mb-4">
                    <!-- Os itens de e-mail serão inseridos aqui -->
                    
                </div>
            </form>
        </div>
        <div class="divider lg:divider-horizontal">
        </div> 
        <div class="">
            <div class="overflow-x-auto">
                <table class="table" id="tabela_disciplinas">                                                                
                
                </table>                        
            </div>            
        </div>
    
    </div>
    <script>
        
        var insertedValues = [];
        function generateTable() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "' . $arrConfig['url_admin'] . 'dashboards/gerar_tabelas_views_disciplinas.php?valor=disciplinas' . '", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("tabela_disciplinas").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        generateTable();
        

        document.addEventListener(\'DOMContentLoaded\', function() {
            
            $(\'#disciplinas-input\').select2();
                        
           
            document.getElementById("disciplinas-input").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); 
                    document.getElementById("add-disciplinas").click();
                }
            });
    
            document.getElementById("add-disciplinas").onclick = function() {                
                var disciplinaInput = document.getElementById("disciplinas-input");
                var disciplinaList = document.getElementById("disciplinas-list");
                var disciplinaId = disciplinaInput.value.trim();
                var disciplinaText = disciplinaInput.options[disciplinaInput.selectedIndex].text;
                
                
                
    
                if(disciplinaId) {    
                    var flag = false;                
                    insertedValues.forEach(function(value) {
                        if(value == disciplinaId) {
                            Swal.fire({
                                title: "Disciplina já inserida",
                                text: "A disciplina já foi inserida",
                                icon: "error",
                                timer: 2000,
                                
                            })
                            flag = true;
                            return;
                        }
                    });
                    if(!flag) {

                        var newDiv = document.createElement("div");
                        newDiv.className = "flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs";
                        
                        var newInput = document.createElement("input");
                        newInput.type = "hidden";
                        newInput.name = "disciplinas[]";
                        newInput.value = disciplinaId;
                        
                        newDiv.innerHTML = `<span class="flex-auto">${disciplinaText}</span>
                                    <button type="button"
                                            onclick="removedisciplina(this)"
                                            class="flex-none text-xs bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                        Remover
                                    </button>`;
                        newDiv.appendChild(newInput);                        
                        disciplinaList.appendChild(newDiv);                        
                        disciplinaInput.value = "";
                        insertedValues.push(disciplinaId);
                    }
                }
            };
            
        })
        function removedisciplina(button) {        
            button.parentElement.remove();
            var index = insertedValues.indexOf(button.parentElement.querySelector("input").value);
                if (index > -1) {
                    insertedValues.splice(index, 1);
                }
            
        }
    </script>

    ';

    return $html;

}

function esforco_direcao_turma() {
    global $arrConfig;
    $id_curso = $_SESSION['id_curso'];
    $id_turma = $_GET['id_turma'];

    $sql = "SELECT esforco.* 
    FROM turma 
    INNER JOIN esforco 
    WHERE turma.id_esforco = esforco.id AND turma.id = " . $id_turma;
    $res = my_query($sql);
    /* pr($res); */

    $html = '<form method="post" action="' . $arrConfig['url_modules'] . 'trata_esforco_turma.mod.php?id_turma=' . $id_turma . '" class="overflow-x-auto">';

    $html .= '
    <div class="overflow-x-auto">
        
        <table class="table">
            <!-- head -->
            <thead>
                <tr>                    
                    <th>Esforço</th>
                    <th>Barreira</th>
                    <th>Segunda-Feira</th>
                    <th>Terça-Feira</th>
                    <th>Quarta-Feira</th>
                    <th>Quinta-Feira</th>
                    <th>Sexta-Feira</th>
                    <th>Sábado</th>
                    <th>Domingo</th>
                    
                    <th> </th>
                </tr>
            </thead>
            <tbody>
                <!-- row 1 -->
                <tr class="">
                ';
                

    $res = array_shift($res);
    
    
    if(isset($_GET['editar'])) {

        $html .= '
        <input type="hidden" name="id_esforco" value="' . $res['id'] . '">
        <td>
            <label class="form-control w-full max-w-xs">                        
                <input type="number" placeholder="0" name="limite" value="' . $res['limite'] . '" class="input w-14 max-w-xs" />
            </label>
        </td>
        <td>
            <label class="form-control w-full max-w-xs">                            
                <input type="number" placeholder="0" name="barreira" value="' . $res['barreira'] . '" class="input w-14 max-w-xs" />
            </label>
        </td>
        ';
    } else {
        $html .= '                    
        <td>
            <label class="form-control w-full max-w-xs">                        
                <input type="number" placeholder="0" value="' . $res['limite'] . '" class="input w-14 max-w-xs" disabled />
            </label>
        </td>
        <td>
            <label class="form-control w-full max-w-xs">                            
                <input type="number" placeholder="0" value="' . $res['barreira'] . '" class="input w-14 max-w-xs" disabled />
            </label>
        </td>
        ';
    }

    foreach($res as $k => $v) {
        if($k == 'id' || $k == 'limite' || $k == 'barreira' || $k == 'ativo') {
            continue;
        }
        if(isset($_GET['editar'])) {                                                
            $html .= '
            <td>
                <label class="form-control w-full max-w-xs">                            
                    <input type="checkbox" name="' . $k . '" class="toggle" ';  $html .= $v ? 'checked' : ''; $html .=  ' />
                </label>
            </td>
            ';            
        } else {
            $html .= '
            <td>
                <label class="form-control w-full max-w-xs">                            
                    <input type="checkbox" class="toggle" ';  $html .= $v ? 'checked' : ''; $html .=  ' disabled />
                </label>
            </td>
            ';
        }
    }
    
    if(isset($_GET['editar'])) {
        $html .= '
        <td>
            <input type="submit" class="btn btn-ghost btn-xs" value="Confirmar">            
        </td>
        ';
    } else {
        $html .= '
    
                <td>
                    <a href="?id_turma=' . $_GET['id_turma'] . '&editar=true" class="btn btn-ghost btn-xs">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
    
    ';
    }

    $html .= '                                             
            </tbody>
        </table>
    </div>
    
    ';
    $html .= '</form>';

    return $html;
}

function criar_atividade_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];

    $sql = "SELECT curso.id FROM curso 
    INNER JOIN turma ON curso.id = turma.id_curso
    WHERE turma.id = " . $id_turma;
    $res = my_query($sql);
    $_SESSION['id_curso'] = $res[0]['id'];

    $id_curso = $_SESSION['id_curso'];
    
    $html = '
    <div class="flex flex-row max-w-full">
        <form method="post" class="w-4/12" action="' . $arrConfig['url_modules'] . 'trata_criar_atividade_turma.mod.php?id_turma=' . $id_turma . '" class="overflow-x-auto">
            <div class="flex flex-col gap-6 ml-8">
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Nome da atividade*</span>
                        </div>
                        <input name="titulo" required type="text" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Breve descrição*</span>
                        </div>
                        <input type="text" required name="descricao" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                </div>
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Data de inicio*</span>
                        </div>
                        <input type="date" required name="comeco" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Data de conclusão*</span>
                        </div>
                        <input type="date" required name="fim" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                </div>
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Tipo da atividade</span>
                        </div>
                        <input type="text" name="tipo" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Disciplina*</span>                            
                        </div>
                        <select name="disciplina" class="select select-bordered">
                            <option disabled selected>Escolha uma.</option>
                            ';

    $arr_disciplinas = buscar_disciplinas_cargo($_SESSION['id'], 'professor', $id_curso);
    foreach($arr_disciplinas as $disciplina) {
        $sql = "SELECT * FROM disciplinas WHERE id = " . $disciplina['id_disciplina'];
        $res = my_query($sql);
        $html .= '<option value="' . $res[0]['id'] . '">' . $res[0]['abreviatura'] . ' - ' . $res[0]['nome'] . '</option>';
    }            

    $html .= '                                                        
                        </select>                        
                    </label>
                </div>
                <input type="hidden" name="id_turma" value="' . $id_turma . '">
                <input type="hidden" name="id_professor" value="' . $_SESSION['id'] . '">
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Tempo sugerido em horas*</span>
                        </div>
                        <input type="number" required name="tempo_sugerido" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" />
                    </label>
                    <label class="form-control mt-auto w-full max-w-xs">
        
                        <button class="btn w-full">Criar</button>
                    </label>
                </div>
        
            </div>
        
        </form>
        <div class="divider lg:divider-horizontal"></div>
        <div class="max-w-full flex-grow ">
            ';
            $html .= gerar_calendario_atividades();
        $html .= '
        </div>
    </div>
    ';

    return $html;
}

function agenda_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];

    $sql = "SELECT eventos.*, atividades.* FROM atividades 
    INNER JOIN rel_atividades_turma ON rel_atividades_turma.id_atividade = atividades.id 
    INNER JOIN eventos ON eventos.id = atividades.id_evento
    WHERE rel_atividades_turma.id_turma = " . $id_turma;        
    $res = my_query($sql);
                
    $eventos_modelo = [];
    
    foreach($res as $vEvento) {
        $novo_evento = [];
        
        foreach($vEvento as $kProp => $vProp) {
            switch($kProp) {
                case 'titulo':
                    $novo_evento['title'] = $vProp;
                    break;                
                case 'fim':
                    $date = new DateTime($vProp);
                    $date->modify('-1 day');                    
                    $novo_evento['start'] = $date->format('Y-m-d H:i:s');

                    $date->modify('+1 day');
                    $novo_evento['end'] = $date->format('Y-m-d H:i:s');
                    break;
                case 'id_disciplina': 
                    $sql = "SELECT nome FROM disciplinas WHERE id = " . $vProp;
                    $res = my_query($sql);
                    $novo_evento['extendedProps']['disciplina'] = $res[0]['nome'];
                    break;

                
                default:
                    $novo_evento['extendedProps'][$kProp] = $vProp;            
            }
        }
        
        $eventos_modelo[] = $novo_evento;
        
    }
    
    

    
    $html = gerar_calendario($eventos_modelo, 'listMonth');    

    

    return $html;
}
