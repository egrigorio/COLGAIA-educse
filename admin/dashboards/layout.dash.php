<?php
include '../include/config.inc.php';
include_once 'configuracoes.adm.php';
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
        
        foreach($arr_items as $item) {
            if(isset($item['nome_curso'])) {
                if($query == '') {
                    echo '
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?" class="btn btn-ghost text-sm btn-active">' . $item['abreviatura'] . '</a>
                    ';
                } else {
                    echo '
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?" class="btn btn-ghost text-sm">' . $item['abreviatura'] . '</a>
                    ';
                }
            } else {
            if($query == 'id_turma=' . $item['id']) {
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
                        <li><a>Logout</a></li>
                    </ul>
                    
                </div>
            </div>
    </div>
    
    ';
}

function turma($arr_turma, &$flag_direcao_turma) {
    
    if($arr_turma[0]['id_diretor_turma'] == $_SESSION['id']) {
        $flag_direcao_turma = true;
    }
    echo '
    
    <div class="bg-blue-200 flex justify-center w-full h-52 text-center">
        <b>
            <h1 class="text-blue-950 bold mt-20 text-4xl">' . $arr_turma[0]['nome_turma'] . '</h1>';
    echo $flag_direcao_turma ? '<span class="flex items-center justify-center">diretor de turma</span>' : '';              
    echo '
        </b>
    </div>
    <div role="tablist" class="bg-blue-200 tabs tabs-lifted">

    ';   
    $tabs = $flag_direcao_turma ? 'tabs_direcao_turma' : 'tabs_turma';
    def_config_adm($tabs, $arr_config);
    foreach($arr_config as $kCampos => $vCampos) {
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
    
    echo '
    
    <div class="bg-blue-200 flex justify-center w-full h-52 ">
        <b>
            <h1 class="text-blue-950 bold mt-20 text-4xl">' . $res[0]['nome_curso'] . '</h1> 
            <span class="flex items-center justify-center">' . $res[0]['abreviatura'] . '</span>   
        </b>
    </div>
    <div role="tablist" class="bg-blue-200 tabs tabs-lifted">
    ';
    def_config_adm('tabs_curso', $arr_config);
    foreach($arr_config as $kCampos => $vCampos) {
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
    

    $sql = "SELECT users.*, estado FROM users
        JOIN rel_user_curso ON users.id = rel_user_curso.id_user
        JOIN curso ON rel_user_curso.id_curso = curso.id
        WHERE curso.id_diretor_curso = " . $_SESSION['id'] . " AND rel_user_curso.cargo = 'professor'";
    $res = my_query($sql);
    $arr_professores = array();
    foreach ($res as $row) {
        $arr_professores[] = $row;
    }
    $res = my_query($sql);
    $arr_turmas = array();
    $sql = "SELECT id FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
    $res2 = my_query($sql);
    /* pr($res); */
    $id_curso = $res2[0]['id'];
    $_SESSION['id_curso'] = $id_curso;
    $arr_turmas = buscar_turmas_curso($id_curso);    

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
            <div class="overflow-x-auto">
                <table class="table">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th>
                                <label>
                                <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <th>Nome</th>
                            <th>Disciplinas</th>
                            <th>Turmas</th>
                            <th>Direção de Turma</th>
                            <th>Opções</th>
                        </tr>
                    </thead>
                    <!-- head -->                                        
                    <tbody>          
                    
                        ';
                        
                    foreach($res as $professor) {
                        if($professor['estado'] == '1') {
                            
                            $direcao_turma = '';
                            $direcao_turma = buscar_direcao_turma($professor['id']);
                            $disciplinas = buscar_disciplinas_cargo($professor['id'], 'professor', $id_curso); 
                            $arr_nome_turmas_participa = buscar_nome_turmas_participa_curso($professor['id'], $id_curso);
                            count($direcao_turma) == 0 ? $direcao_turma = 'Nenhuma turma' : $direcao_turma = $direcao_turma[0]['nome_turma'];
                            $nome_turmas_participa = '';
                            foreach($arr_nome_turmas_participa as $turma) {
                                $nome_turmas_participa .= $turma['nome_turma'] . ', ';
                            }
                            $nome_turmas_participa != '' ? $nome_turmas_participa = substr($nome_turmas_participa, 0, -2) : $nome_turmas_participa = 'Nenhuma turma';
                            $html .= '
                            
                            <tr>
                                <th>
                                    <label>
                                    <input type="checkbox" class="checkbox" />
                                    </label>
                                </th>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12">
                                            <img src="' . $arrConfig['url_pfp'] . $professor['pfp'] . '" alt="PFP do ' . $professor['username'] . '" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">' . $professor['username'] . '</div>
                                            <div class="text-sm opacity-50">' . $professor['email'] . '</div>
                                        </div>
                                    </div>
                                </td>
                                
                                    ';
                                    $nome_disciplinas = '';
                                    foreach($disciplinas as $disciplina) {
                                        
                                        $sql = "SELECT abreviatura FROM disciplinas WHERE id = " . $disciplina['id_disciplina'];
                                        $resDisc = my_query($sql);
                                        
                                        $nome_disciplinas .= $resDisc[0]['abreviatura'] . ', '; 
                                    }
                                    $nome_disciplinas != '' ? $nome_disciplinas = substr($nome_disciplinas, 0, -2) : $nome_disciplinas = 'Nenhuma disciplina';
                                    
                                    $html .= '
                                    <td>
                                        ' . $nome_disciplinas . '
                                        <br/>
                                        <span class="badge badge-ghost badge-sm">Disciplinas</span>
                                    </td>
                                    ';
                                    $html .= '
                                    <td>' . $nome_turmas_participa . '</td>
                                    <td>' . $direcao_turma . '</td>
                                    <th>
                                        <a href="' . $arrConfig['url_admin'] . 'editar/professores_curso.php?id_user=' . $professor['id'] .  '" class="btn btn-ghost btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        |                                                                                
                                        <a onClick="
                                        
                                            Swal.fire({
                                                title: \'Tem certeza que deseja remover o user?\',
                                                text: \'Essa ação não pode ser revertida!\',
                                                icon: \'warning\',
                                                showCancelButton: true,
                                                confirmButtonColor: \'#3085d6\',
                                                cancelButtonColor: \'#d33\',
                                                cancelButtonText: \'Cancelar\',
                                                confirmButtonText: \'Sim, remover!\'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $professor['id'] . '\';
                                                    
                                                }
                                            });

                                        " class="btn btn-ghost btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        
                                            
                                        
                                    </th>
                                </tr>
        
                                ';
                            } else {
                                $html .= '<tr>
                                <th>
                                    <label>
                                    <input type="checkbox" class="checkbox" />
                                    </label>
                                </th>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12">
                                            <img src="' . $arrConfig['url_pfp'] . $professor['pfp'] . '" alt="PFP do ' . $professor['username'] . '" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">' . $professor['username'] . '</div>
                                            <div class="text-sm opacity-50">' . $professor['email'] . '</div>
                                        </div>
                                    </div>
                                </td>
                                
                                    ';
                                    
                                    
                                    
    
                                    $html .= '   
                                    
                                    <td>Estado:' . $professor['estado'] . '</td>
                                    <td> </td>
                                    <th>
                                        <a class="btn btn-ghost btn-xs">Reforçar convite</a>
                                    </th>
                                    <th>
                                        <a href="' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $professor['id'] . '" class="btn btn-ghost btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </th>
                                    
                                    
                            </tr>';
                            }
                        }
    
                        $html .= '
    
                            
                        </tbody>
                        <!-- foot -->
                        <tfoot>
                            <tr>
                                <th> </th>
                                <th>Nome</th>
                                <th>Disciplinas</th>
                                <th>Turmas</th>
                                <th>Direção de Turma</th>
                                <th>Opções</th>
                            </tr>
                        </tfoot>
                    
                    </table>
                </div>
            </div>
        </div>
    
        <script>
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
        
                                    
                                