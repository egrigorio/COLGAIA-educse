<?php
/* include '../../include/config.inc.php'; */
include_once $arrConfig['dir_admin'] . 'dashboards/gerar_calendario_atividades.php';
include_once $arrConfig['dir_admin'] . 'configuracoes.adm.php';
require_once $arrConfig['dir_site'] . '/vendor/autoload.php';

if(isset($_GET['id_turma'])) {
    $id_turma = $_GET['id_turma'];
    $sql = "SELECT curso.id FROM curso 
        INNER JOIN turma ON curso.id = turma.id_curso
        WHERE turma.id = " . $id_turma;
        $res = my_query($sql);
    $_SESSION['id_curso'] = $res[0]['id'];
}

function navbar($arr_items) {
    global $arrConfig;
    $teste = parse_url($_SERVER['REQUEST_URI']);
    $query = isset($teste['query']) ? $teste['query'] : '';
    $al = isset($_GET['al']) ? $_GET['al'] : '';
    if($al) {
        $ano_letivo = get_proximo_ano_letivo(get_ano_letivo());
    } else {
        $ano_letivo = get_ano_letivo();
    }
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
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?' . (isset($_GET['al']) ? '&al=true' : '') . '" class="btn btn-ghost text-sm btn-active">' . $item['abreviatura'] . '</a>
                    ';
            } else {
                echo '
                    <a href="' . $arrConfig['url_admin'] . 'curso.php?' . (isset($_GET['al']) ? '&al=true' : '') . '" class="btn btn-ghost text-sm">' . $item['abreviatura'] . '</a>
                    ';
            }
        } else {
            if ($query == 'id_turma=' . $item['id']) {
                echo '
                <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $item['id'] . '' . (isset($_GET['al']) ? '&al=true' : '') . '" class="btn btn-ghost text-sm btn-active">' . $item['nome_turma'] . '</a>
                ';
            } else {
                echo '
                <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $item['id'] . '' . (isset($_GET['al']) ? '&al=true' : '') . '" class="btn btn-ghost text-sm">' . $item['nome_turma'] . '</a>
                ';
            }
        }
    }

    echo '
            
        </div>
        <div class="navbar-end">   

            <div class="tooltip tooltip-left" data-tip="Indica o ano letivo da vista das turmas">
                <span class="text text-xs badge badge-primary">' . $ano_letivo . '</span>  <!-- adicionar aqui uma tooltip -->
            </div>
            <!-- <button class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div> 
            </button> -->

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Tailwind CSS Navbar component" src="' . $arrConfig['url_pfp'] . $_SESSION['pfp'] . '" />
                    </div>
                </div>
                
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <!-- <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                        </li>                        
                        <li><a>Settings</a></li>
                        -->
                        '; 
                        
                        $sql = "SELECT * FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
                        $res = my_query($sql);
                        if($res) {
                            echo '<li><a href="' . (isset($_GET['al']) ? ($arrConfig['url_admin'] . 'curso.php') : '?al=true') . '">Vista de ' . (isset($_GET['al']) ? get_ano_letivo() : get_proximo_ano_letivo(get_ano_letivo())) . '</a></li>';
                        }

                        echo '
                        <li><a href="' . $arrConfig['url_modules'] . 'trata_logout.mod.php' . '">Logout</a></li>
                    </ul>
                    
                </div>
            </div>
    </div>
    
    ';
}

function turma($arr_turma, &$flag_direcao_turma, $aluno = false) {
    $flag_tabs = false;
    $flag_tab_get = isset($_GET['tab']) ? true : false;

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
    <div class="">
        
        <div role="tablist" class="tabs tabs-lifted">
        ';
        $tabs = ($aluno ? 'tabs_aluno' : ($flag_direcao_turma ? 'tabs_direcao_turma' : 'tabs_turma'));
        def_config_adm($tabs, $arr_config);
        foreach ($arr_config as $kCampos => $vCampos) {
            if($flag_tab_get) {
                if(strtolower($vCampos['label']) == $_GET['tab']) {
                    $vCampos['checked'] = 1;
                    $flag_tabs = true;
                } else {
                    $flag_tabs = false;
                }
            }
            echo '
            <input type="radio" name="' . $vCampos['name'] . '" role="tab" class="tab" aria-label="' . $vCampos['label'] . '" ' . ($vCampos['checked'] == 1 ? 'checked' : '')  . ' />
        
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                ' . $vCampos['content'] . '
            </div>
        
            ';
        }
        echo '
        </div>
    </div>
    ';

}


function instituicao($arr_instituicao) {
    $flag_tab_get = isset($_GET['tab']) ? true : false;

    echo '
    
    <div class="bg-primary flex justify-center w-full h-52 text-center">
        <b>
            <h1 class="bold mt-20 text-4xl">' . $arr_instituicao[0]['nome'] . '</h1>';
    
    echo '
        </b>
    </div>
    <div class="">
        
        <div role="tablist" class="tabs tabs-lifted">
        ';
        
        def_config_adm('tabs_instituicao', $arr_config);
        foreach ($arr_config as $kCampos => $vCampos) {
            if($flag_tab_get) {                
                if(strtolower($vCampos['label']) == $_GET['tab']) {
                    $vCampos['checked'] = 1;
                    $flag_tabs = true;
                } else {
                    $vCampos['checked'] = 0;
                    $flag_tabs = false;
                }
            }
            echo '
            <input type="radio" name="' . $vCampos['name'] . '" role="tab" class="tab" aria-label="' . $vCampos['label'] . '" ' . ($vCampos['checked'] == 1 ? 'checked' : '')  . ' />
        
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                ' . $vCampos['content'] . '
            </div>
        
            ';
        }
        echo '
        </div>
    </div>
    ';

}

function curso() {
    $flag_tab_get = isset($_GET['tab']) ? true : false;
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
        if($flag_tab_get) {
            if(strtolower($vCampos['label']) == $_GET['tab']) {                
                $vCampos['checked'] = 1;
                $flag_tabs = true;
            } else {                
                $vCampos['checked'] = 0;
                $flag_tabs = false;
            }
        }       
        
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
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_user_curso.mod.php?cargo=professor&id_curso=' . $id_curso . '" id="emailForm" enctype="multipart/form-data" >
                <div class="flex mb-4 gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                        <input type="text" class="grow" id="email-input" placeholder="Email" />
                    </label>
                    <button type="button" id="add-email" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button type="submit" id="submit_form_prof" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                    <button type="button" id="import-csv-prof" class="btn btn-ghost text-xs py-1 px-2">
                        Importar CSV
                    </button>
                    <input type="file" name="csv" id="abrir-dialog-csv-prof" accept=".csv" style="display: none;"  />
                </div>
                <div id="email-list" class="mb-4">
                    <div class="flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs">
                        <span id="nome-arquivo-prof" class="flex-auto"></span>
                    </div>
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
            <div class="overflow-x-auto max-h-96">
                <table class="table table-sm" id="tabela_professores">                                                                
                    
                </table>                        
            </div>
            
        </div>
    </div>
        <script>
            var btn_submit_prof = document.getElementById(\'submit_form_prof\');
            btn_submit_prof.addEventListener(\'click\', function() {
                document.getElementById(\'loading-overlay\').classList.remove(\'hidden\');
            });

            var input_csv = document.getElementById(\'abrir-dialog-csv-prof\');
            input_csv.addEventListener(\'change\', function() {
                var nome_arquivo = document.getElementById(\'nome-arquivo-prof\');
                nome_arquivo.innerHTML = \'Nome do arquivo importado: \' + this.files[0].name;
            });

            var botao = document.getElementById(\'import-csv-prof\');
            botao.addEventListener(\'click\', function() {
                document.getElementById(\'abrir-dialog-csv-prof\').click();
            });
            document.getElementById(\'select_tabelas\').addEventListener(\'change\', function() {
                
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
                email = email.toLowerCase();
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
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_user_curso.mod.php?cargo=aluno&id_curso=' . $id_curso . '" id="add-aluno_form" enctype="multipart/form-data" >
                <div class="flex mb-4 gap-2">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                        <input type="text" class="grow" id="email-input_alunos" placeholder="Email" />
                    </label>
                    <button type="button" id="add-email_alunos" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button id="submit-form-alunos" type="submit" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                    <button type="button" id="import-csv" class="btn btn-ghost text-xs py-1 px-2">
                        Importar CSV
                    </button>
                    <input type="file" name="csv" id="abrir-dialog-csv" accept=".csv" style="display: none;"  />
                </div>
                    
                <div id="email-list_alunos" class="mb-4">
                    <div class="flex items-center bg-base-100 px-3 py-1 rounded shadow mb-1 text-xs">
                        <span id="nome-arquivo" class="flex-auto"></span>
                    </div>
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
            <div class="overflow-x-auto max-h-96">
                <form method="POST" action="' . $arrConfig['url_modules'] . 'trata_inserir_aluno_turma.mod.php" id="form_edicao_alunos_curso">                    
                        <table class="table table-sm" id="tabela_alunos">
                    
                        </table>
                </form>
            </div>            
        </div>
        
    </div>
    

    <script>

        var btn_submit_alunos = document.getElementById(\'submit-form-alunos\');
        btn_submit_alunos.addEventListener(\'click\', function() {
            document.getElementById(\'loading-overlay\').classList.remove(\'hidden\');
        });

        var input_csv = document.getElementById(\'abrir-dialog-csv\');
        input_csv.addEventListener(\'change\', function() {
            var nome_arquivo = document.getElementById(\'nome-arquivo\');
            nome_arquivo.innerHTML = \'Nome do arquivo importado: \' + this.files[0].name;
        });

        var botao = document.getElementById(\'import-csv\');
        botao.addEventListener(\'click\', function() {
            document.getElementById(\'abrir-dialog-csv\').click();
        });

        document.getElementById(\'select_tabelas_alunos\').addEventListener(\'change\', function() {
            
            var valorSelecionado = this.value;
            var tabela = document.getElementById(\'tabela_alunos\');

            tabela.innerHTML = "";
            var xhr = new XMLHttpRequest();
                xhr.open(\'GET\', \'' . $arrConfig['url_admin'] . 'dashboards/' . 'gerar_tabelas_views_alunos.php?' . (isset($_GET['editar']) ? 'editar=true&' : '') . 'valor=\' + valorSelecionado, true);
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
            email = email.toLowerCase();
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

    if(isset($_SESSION['error']) && $_SESSION['error'] != '') {
        $html .= '
        <script>
            Swal.fire({
                title: "Erro",
                text: "' . $_SESSION['error'] . '",
                icon: "error",
                timer: 2000,
                
            })
        </script>
        ';
        unset($_SESSION['error']);
    }
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
                <div class="overflow-x-auto">
                    <div id="disciplinas-list" class="mb-4 h-56">
                        <!-- Os itens de e-mail serão inseridos aqui -->                    
                    </div>
                </div>
            </form>
        </div>
        <div class="divider lg:divider-horizontal">
        </div> 
        <div class="">
            <div class="overflow-x-auto max-h-96">
                <form method="post" action="' . $arrConfig['url_modules'] . 'trata_editar_disciplina_curso.mod.php" id="form_disciplinas">
                    <table class="table" id="tabela_disciplinas">                                                                
                
                    </table>  
                </form>                      
            </div>            
        </div>
    
    </div>
    <script>
        
        var insertedValues = [];
        function generateTable() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "' . $arrConfig['url_admin'] . 'dashboards/gerar_tabelas_views_disciplinas.php?valor=disciplinas' . (isset($_GET['editar']) ? '&editar=true' : '') . '' . '", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("tabela_disciplinas").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        generateTable();
        

        document.addEventListener(\'DOMContentLoaded\', function() {                                                
           
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
                <div class="tooltip" data-tip="Esforço limite máximo, em horas, da turma">
                    <input type="number" required placeholder="0" name="limite" value="' . $res['limite'] . '" class="input w-14 max-w-xs" />
                </div>
            </label>
        </td>
        <td>
            <div class="tooltip" data-tip="Valor inferior ao limite, para servir de alerta a criação de atividades">
                <label class="form-control w-full max-w-xs">
                    <input type="number" required placeholder="0" name="barreira" value="' . $res['barreira'] . '" class="input w-14 max-w-xs" />
                </label>
            </div>
        </td>
        ';
    } else {
        $html .= '                    
        <td>
            <div class="tooltip tooltip-right" data-tip="Esforço limite máximo, em horas, da turma">
                <label class="form-control w-full max-w-xs">                        
                    <input type="number" placeholder="0" value="' . $res['limite'] . '" class="input w-14 max-w-xs" disabled />
                </label>
            </div>
        </td>
        <td>
            <div class="tooltip tooltip-right" data-tip="Valor inferior ao limite, para servir de alerta a criação de atividades">
                <label class="form-control w-full max-w-xs">                            
                    <input type="number" placeholder="0" value="' . $res['barreira'] . '" class="input w-14 max-w-xs" disabled />
                </label>
            </div>
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
                    <input type="checkbox" class="toggle [--tglbg:'; $html .= $v ? 'green' : 'red'; $html .= ']" ';  $html .= $v ? ' checked' : ''; $html .=  ' disabled />
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
                    <a href="?id_turma=' . $_GET['id_turma'] . '&editar=true&tab=esforço" class="btn btn-ghost btn-xs">
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

function criar_atividade_turma($editar = false, $id_evento = null) {

    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    $id_curso = (isset($_SESSION['id_curso']) ? $_SESSION['id_curso'] : '');
    if($editar) {

        $sql = "SELECT atividades.*, eventos.* FROM atividades 
        INNER JOIN eventos ON eventos.id = atividades.id_evento
        WHERE atividades.id_evento = " . $id_evento . " AND atividades.id_professor = " . $_SESSION['id'];
        $res = my_query($sql);     
                   
        $res = array_shift($res);
        if(!$res) {
            header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
        } 
        $rand = rand(8999, 8990);
        $html = gerar_formulario_edicao($id_turma, $id_curso, $rand, $res);
        return $html;                    
    } else {        
        $rand = rand(9999, 9990);
        $html = gerar_formulario_edicao($id_turma, $id_curso, $rand);
        return $html;
    }
    
}

function gerar_formulario_edicao($id_turma, $id_curso, $rand, $valores_ja_inseridos = null) {
    global $arrConfig;
    $id_user = $_SESSION['id'];    
    $html = '';
    if(isset($_SESSION['erro'])) {
        $html .= '
            <script>
                Swal.fire({
                    title: "Erro",
                    text: "' . $_SESSION['erro'] . '",
                    icon: "error",
                    timer: 7000,
                    
                })
            </script>
        ';
        unset($_SESSION['erro']);
    } 
    $arr_disciplinas = buscar_disciplinas_cargo($_SESSION['id'], 'professor', $id_curso);
    if(!$arr_disciplinas) {
        $html .= '
            <div class="flex min-h-24">
                <span class="text-center">Você não tem disciplinas associadas a essa turma para poder criar atividades.</span>
            </div>
        ';
        return $html;
    } else {
        $html .= '
            ' . ($valores_ja_inseridos ? '<h1 class="text-center text-xl font-bold">Editando evento</h1>' : '') . '
            <div class="flex flex-row max-w-full">
                <form method="post" class="w-4/12" action="' . $arrConfig['url_modules'] . 'trata_' . ($valores_ja_inseridos ? 'editar' : 'criar') . '_atividade_turma.mod.php?id_turma=' . $id_turma . '" class="overflow-x-auto">
                    <div class="flex flex-col gap-6 ml-8">
                        ' . ($valores_ja_inseridos ? '<input type="hidden" name="id_evento" value=' . $valores_ja_inseridos['id_evento'] . '>' : '') . '
                        <div class="flex flex-row gap-8">
                            <label class="form-control w-full max-w-xs">
                                <div class="label">
                                    <span class="label-text">Nome da atividade*</span>
                                </div>
                                <input name="titulo" required type="text" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value="' . $valores_ja_inseridos['titulo'] : '') . '" />
                            </label>
                            <label class="form-control w-full max-w-xs">
                                <div class="label">
                                    <span class="label-text">Breve descrição*</span>
                                </div>
                                <input type="text" required name="descricao" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value="' . $valores_ja_inseridos['descricao'] : '') . '" />
                            </label>
                        </div>
                        <div class="flex flex-row gap-8">
                            <label class="form-control w-full max-w-xs">
                                <div class="label">
                                    <span class="label-text">Data de inicio*</span>
                                </div>
                                <input type="date" required name="comeco" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value=' . $valores_ja_inseridos['comeco'] : '') . ' />
                            </label>
                            <label class="form-control w-full max-w-xs">
                                <div class="label">
                                    <span class="label-text">Data de conclusão*</span>
                                </div>
                                <input type="date" required name="fim" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value=' . $valores_ja_inseridos['fim'] : "") . ' />
                            </label>
                        </div>
                        <div class="flex flex-row gap-8">
                            <label class="form-control w-full max-w-xs">
                                <div class="label">
                                    <span class="label-text">Tipo da atividade</span>
                                </div>
                                <input type="text" required name="tipo" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value="' . $valores_ja_inseridos['tipo'] : "") . '" />
                            </label>
                            
                                <label class="form-control w-full max-w-xs">
                                    
                                    <div class="label">
                                        <span class="label-text">Disciplina*</span>                            
                                    </div>
                                    
                                    <select name="disciplina" class="select select-bordered" required>                            
                                    ';

            
            /* pr($arr_disciplinas); */
            foreach($arr_disciplinas as $disciplina) {
                $sql = "SELECT * FROM disciplinas 
                INNER JOIN rel_disciplina_turma ON disciplinas.id = rel_disciplina_turma.id_disciplina
                WHERE disciplinas.id = " . $disciplina['id_disciplina'] . " AND rel_disciplina_turma.id_turma = " . $id_turma . " AND disciplinas.ativo = 1";
                            
                $res = my_query($sql);        
                if(count($res) > 0) {                    
                $html .= '<option value="' . $res[0]['id_disciplina'] . '">' . $res[0]['abreviatura'] . ' - ' . $res[0]['nome'] . '</option>';
                }
            }            
            $turno = isset($_GET['turno']) ? $_GET['turno'] : -1;
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
                                <input type="number" required name="tempo_sugerido" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" ' . ($valores_ja_inseridos ? 'value=' . $valores_ja_inseridos['tempo_sugerido'] : '') . ' />
                            </label>
                            <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Filtre pelo turno</span>                    
                        </div>             
                        <select name="turno" onchange="trata_onchange_select_turno_criar_atv()" class="select select-bordered mb-5" id="select_turno_criar_atv">                    
                            <option value="-1" ' . ($valores_ja_inseridos ? ($valores_ja_inseridos['id_turno'] == -1 ? 'selected' : '') : '') . '>Turma toda</option>
                            ';                    
                            $sql = "SELECT DISTINCT num_turno, id_turno FROM view_user_turma_turno WHERE id_turma = $id_turma AND num_turno <> 0";
            $res = my_query($sql);
            
            
            foreach($res as $turno) {
                        $html .= '<option value="' . $turno['id_turno'] . '" ' . ($valores_ja_inseridos ? ($valores_ja_inseridos['id_turno'] == $turno['id_turno'] ? 'selected' : '') : '') . '>Turno ' . $turno['num_turno'] . '</option>';
            }            
            $html .= '                
                        </select>
                    </label>
                            </div>
                            <label class="form-control mt-auto w-full">        
                                <button class="btn w-full">' . ($valores_ja_inseridos ? 'Editar' : 'Criar') . '</button>
                            </label>
                
                    </div>
                
                </form>
                <div class="divider lg:divider-horizontal"></div>
                <div id="render-calendar-here" class="max-w-full flex-grow ">
                    <div id="ec' . $rand . '"></div>
                '; 
                
                    
                    $html .= '
                </div>
            </div>
            <script>
                function gerar_calendario(eventos' . $rand . ' = null) {
                    /* eventos' . $rand . '.forEach(function(evento) {
                        if(evento.backgroundColor == \'#1E3A8A\') {
                            evento.backgroundColor = \'red\';                                        
                        }
                    }); */
                    var calendarElement = document.getElementById(\'ec' . $rand . '\');                        
                    if (calendarElement) {
                        calendarElement.innerHTML = "";
                    }            
                    let ec' . $rand . ' = new EventCalendar(document.getElementById(\'ec' . $rand . '\'), {
                        view: \'dayGridMonth\',
                        allDaySlot: false,
                        eventStartEditable: false,
                        eventClassNames: function(eventInfo) {
                            console.log(eventInfo.event.extendedProps.esforco);
                            
                            switch(Number(eventInfo.event.extendedProps.esforco)) {
                                case 1:
                                    console.log(\'aqui 1\')
                                    return [\'bg-red-400\'];
                                    break;
                                case 2:
                                    console.log(\'aqui 2\')
                                    return [\'bg-yellow-200\'];
                                    break;
                                case 3:
                                    console.log(\'aqui 3\')
                                    return [\'bg-green-400\'];
                                    break;                                             
                            }
                            
                            

                        },
                        views: {
                            listMonth: {                    
                                eventContent: function (arg) {
                                let arrayOfDomNodes = [];
                                let title = document.createElement("t");
                                title.innerHTML =
                                    arg.event.title +
                                    " - " + (' . $id_user . ' == arg.event.extendedProps.id_professor ? "(<a href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=edicao&id_evento=" + arg.event.extendedProps.id_evento + "\'>editar</a>)(<a href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=details&tab=agenda&id_evento=" + arg.event.extendedProps.id_evento + "\'>detalhes</a>)" : "(<a href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=details&tab=agenda&id_evento=" + arg.event.extendedProps.id_evento + "\'>detalhes</a>)") +                        
                                    "<br><span style=\'font-size: 12px; color: #999\'>Disciplina: " +
                                    arg.event.extendedProps.disciplina +
                                    " | Tipo: " +  arg.event.extendedProps.tipo + " </span>";
                
                                arrayOfDomNodes.push(title);
                                return { domNodes: arrayOfDomNodes };
                                },
                            },
                            dayGridMonth: {
                                eventContent: function (arg) {
                                let arrayOfDomNodes = [];
                                let title = document.createElement("t");
                                title.innerHTML = arg.event.title;
                
                                arrayOfDomNodes.push(title);
                                return { domNodes: arrayOfDomNodes };
                                },
                            },
                        },

                        
                        events: eventos' . $rand . ',
                });    
            
                console.log(eventos' . $rand . ');
                }
                function clique_editar_evento(id_atv) {
                    let url = window.location.href;
                    if (url.indexOf(\'?\') > -1){
                    url += \'&id_atividade=\' + id_atv;
                    

                    } else {
                    url += \'?id_atividade=\' + id_atv;
                    }
                    document.getElementById(\'my_modal_5\').dataset.idAtv = id_atv;
                    my_modal_5.showModal();
                    
                }

                function setCalendarEvents(turno) {
                    var xhr = new XMLHttpRequest();
                    var url = "' . $arrConfig['url_admin'] . 'dashboards/call_func_calendar.php?turno=" + turno + "&id_turma=' . $id_turma . '";
                    var params = "id_turno=" + turno;            
                    xhr.open("POST", url, true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");            
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            
                            var eventos = JSON.parse(xhr.responseText);
                            gerar_calendario(eventos);
                        }
                    };
                    
                    xhr.send(params);
                }

                function trata_onchange_select_turno_criar_atv() {
                    
                    var turno = document.getElementById("select_turno_criar_atv").value;
                    
                    setCalendarEvents(turno);
                }
                var event = new Event(\'change\');
                var select = document.getElementById(\'select_turno_criar_atv\');
                select.dispatchEvent(event);
            </script> 
            ';
    }

    

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
                    if(count($res) > 0) {
                        $novo_evento['extendedProps']['disciplina'] = $res[0]['nome'];
                    }
                    break;

                
                default:
                    $novo_evento['extendedProps'][$kProp] = $vProp;            
            }
        }
        
        $eventos_modelo[] = $novo_evento;
        
    }
    
    

    
    if(isset($_GET['id_evento']) && isset($_GET['tipo'])){
        if($_GET['tipo'] == 'edicao') {
            $html = criar_atividade_turma(true, $_GET['id_evento']);
        } else {
            $html = gerar_detalhes_evento($_GET['id_evento']);
        }
    } else {
        $rand = rand(100, 200);
        $html = gerar_calendario($eventos_modelo, 'listMonth', $rand);

    }
            

    return $html;
}

function gerar_detalhes_evento($id_evento) {
    global $arrConfig;
    $id_evento = $_GET['id_evento'];
    $sql = "SELECT eventos.*, atividades.* FROM atividades 
    INNER JOIN eventos ON eventos.id = atividades.id_evento
    WHERE eventos.id = " . $id_evento;
    $res = my_query($sql);
    
    /* pr($res); */
    $html = '
    
    <div class="flex w-full flex-col h-full gap-8">

        <h1 class="text-center text-lg font-bold">' . $res[0]['titulo'] . '</h1>
        
        <div class="flex gap-8">
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Descrição</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . $res[0]['descricao'] . '" class="input w-full max-w-xs" disabled />                    
            </label>                             
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Começo</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . substr($res[0]['comeco'], 0, 10) . '" class="input w-full max-w-xs" disabled />
            </label>
        
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Fim</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . substr($res[0]['fim'], 0, 10) . '" class="input w-full max-w-xs" disabled />                    
            </label>                                             
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Criado em</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . substr($res[0]['criado_em'], 0, 10) . '" class="input w-full max-w-xs" disabled />                    
            </label>                 
        
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Editado em</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . substr($res[0]['editado_em'], 0, 10) . '" class="input w-full max-w-xs" disabled />                    
            </label>                             
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Tipo</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . $res[0]['tipo'] . '" class="input w-full max-w-xs" disabled />                    
            </label>      
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Tempo sugerido</span>                        
                </div>
                <input type="text" placeholder="Type here" value="' . $res[0]['tempo_sugerido'] . '" class="input w-full max-w-xs" disabled />                    
            </label>                 
        </div>                        
        
        <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '" class="btn btn-ghost">Voltar</a>


    </div>
    
    ';
    return $html;                            

}

function tabela_alunos_diretor_turma() {    
   
    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    $editar = isset($_GET['editar']) ? $_GET['editar'] : '';

    $html = '
    
        <div class="overflow-x-auto">
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Filtre pelo turno</span>                    
                </div>             
                <select onchange="trata_onchange_select_turno()" class="select select-bordered mb-5" id="select_tabelas_alunos">                    
                    <option selected value="all">Todos</option>
                    ';                    
                    /* $sql = "SELECT DISTINCT * FROM view_turnos_id_turma WHERE numero <> 0 AND id_turma = $id_turma ORDER BY numero ASC"; */
                    $sql = "SELECT DISTINCT * FROM view_turno_turma WHERE numero <> 0 AND id_turma = $id_turma ORDER BY numero ASC";

    $res = my_query($sql);
    /* pr($res); */
    
    foreach($res as $turno) {
                if($turno['numero'] != 0) {
                    $html .= '<option value="' . $turno['numero'] . '">Turno ' . $turno['numero'] . '</option>';
                }
    }            
    $html .= '                
                </select>
            </label>
            <hr>
            <div class="overflow-x-auto max-h-96">
                <form method="post" action="' . $arrConfig['url_modules'] . 'trata_editar_turno_user.mod.php">
                    <table class="table" id="tabela_alunos_diretor_turma">
                    </table>
                </form>
            </div>
        </div>
        <script>
            function trata_onchange_select_turno() {
                var valorSelecionado = document.getElementById(\'select_tabelas_alunos\').value;                
                
                var tabeladt = document.getElementById(\'tabela_alunos_diretor_turma\');
                tabeladt.innerHTML = "";
                var xhr = new XMLHttpRequest();
                xhr.open(\'GET\', \'' . $arrConfig['url_admin'] . 'dashboards/gerar_tabela_view_alunos_dt.php?turno_numero=\' + valorSelecionado + \'' .  '&id_turma=' . $id_turma .'' . ($editar ? '&editar=true' : '') . '\', true);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                
                        tabeladt.innerHTML = this.responseText;
                    }
                };
                xhr.send();
            }
            var event = new Event(\'change\');
            var select = document.getElementById(\'select_tabelas_alunos\');
            select.dispatchEvent(event);

        </script>

    ';

    return $html;

}

function tabela_turnos_diretor_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];    
    /* $sql = "SELECT * FROM view_turnos_id_turma WHERE numero <> 0 AND id_turma = $id_turma ORDER BY numero ASC"; */
    $sql = "SELECT DISTINCT * FROM view_turno_turma WHERE numero <> 0 AND id_turma = $id_turma ORDER BY numero ASC";
    $res = my_query($sql);

    $html = '
    <div class="flex flex-row justify-around ">
        <div class="flex flex-col w-2/12">            
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_turno_turma.mod.php' . '">
                <div class="flex flex-row">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Adicionar novo turno?</span>
                            
                        </div>
                        <div class="flex">
                            <input name="id_turma" type="hidden" value="' . $id_turma . '">
                            <input name="novo_turno" required type="number" min="1" placeholder="Número do novo turno" class="input input-bordered w-full max-w-xs" />
                            <!-- min=' . ($res ? ($res[count($res) - 1]['numero'] + 1) : '1') . ' max="' . ($res ? ($res[count($res) - 1]['numero'] + 1) : '1') . '" -->
                            <button class="btn btn-ghost">Adicionar</button>
                        </div>
                    </label>
                </div>
            </form>
        </div>
        <div class="divider lg:divider-horizontal"></div> 
        <div class="flex w-9/12 overflow-x-auto max-h-96">
            <table class="table">
                <!-- head -->
                <thead>
                    <tr>    
                        <th>Número</th>
                        <th>Remover</th>          
                    </tr>
                </thead>
                <tbody>
            '; 
            $numeros = [];
            $cont = 0;
            
            foreach($res as $turno) {
                $cont++;
                if(!in_array($turno['numero'], $numeros)) {
                    $numeros[] = $turno['numero'];
                    $html .= '
                    <tr>
                        <td>' . $turno['numero'] . '</td>
                        <td>
                            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_remover_turno_turma.mod.php' . '">
                                <input type="hidden" name="id_turno" value="' . $turno['id_turno'] . '">
                                <input type="hidden" name="id_turma" value="' . $id_turma . '">
                                <button class="btn btn-ghost btn-sm">Remover</button>
                                ' . /* ($cont == count($res) ? '<button class="btn btn-ghost btn-sm">Remover</button>' : '') */ /* ativar isso caso queira permitir remoção apenas do último turno */ '
                            </form>
                        </td>
                    </tr>
                    ';
                } else {
                    continue;
                }                
            }
            $html .= '     
                </tbody>
                </table>
        </div>
    </div>
    

    ';
    return $html;
}

function painel_gestao_turmas_diretor_curso() { /* adicionar filtros aqui, tipo, turma que tem mais atividades, turmas por ano letivo */    
    global $arrConfig;    
    if(isset($_SESSION['erro'])) {
        echo '
        <script>
            Swal.fire({
                title: "Erro",
                text: "' . $_SESSION['erro'] . '",
                icon: "error",
                timer: 7000,
                
            })
        </script>
        ';
        unset($_SESSION['erro']);
    } else {
        $html = '';
    }    
    $sql = "SELECT * FROM turma WHERE id_curso = " . $_SESSION['id_curso'];
    $res = my_query($sql);
    $flag_tem_turmas = false;
    $flag_tem_turmas = (count($res) > 0 ? true : false);
    $flag_turmas_2anos = false;
    // tratar o res e criar um novo array que tenha os anos letivos
    $anos_letivos = [];
    foreach($res as $turma) {
        if(!in_array($turma['ano_letivo'], $anos_letivos)) {
            $anos_letivos[] = $turma['ano_letivo'];
        }
    }
    $proximo_ano_letivo = get_proximo_ano_letivo(get_ano_letivo());
    $sql = "SELECT * FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
    $res = my_query($sql);
    $abreviatura = $res[0]['abreviatura'];
    $abreviatura = strtoupper($abreviatura);

    $sql = "SELECT id FROM turma WHERE nome_turma = '11º $abreviatura' AND id_curso = 2 AND ano_letivo = '$proximo_ano_letivo'"; 
    $res = my_query($sql);
    if(count($res) > 0) {
        $flag_turmas_2anos = true; // se tiver turmas para o próximo ano letivo, não permitir a criação de turmas para o ano letivo atual
    }

    $html = '
    <div class="flex flex-row justify-between">
        <select onchange="change_select_ano_letivo();" id="slc_ano_letivo" class="select w-full max-w-xs">
            <option disabled>Escolha um ano letivo</option>
            '; 
            foreach($anos_letivos as $ano) {
                $html .= '<option value="' . $ano . '">' . $ano . '</option>';
            }
            $alertTitle = $flag_tem_turmas ? 'Atualizando Turmas' : 'Gerando Turmas';
            $alertText = $flag_tem_turmas ? 'atualizar' : 'gerar';
            $ano_letivo = get_ano_letivo();
            $proximo_ano_letivo = get_proximo_ano_letivo($ano_letivo);

            $html .= '
        </select>
        <form id="form_atualizar_turmas_dc" method="post" action="' . $arrConfig['url_modules'] . 'trata_atualizar_turmas_ano_letivo.mod.php' . '">
            <input type="hidden" name="tem_turmas" value="' . $flag_tem_turmas . '">
            <input type="hidden" name="ano_letivo" value="">
            '; 
            
            if($flag_turmas_2anos) {
                $aletivo = get_proximo_ano_letivo(get_ano_letivo());
                $sql = "SELECT * FROM turma WHERE ano_letivo = '$proximo_ano_letivo' AND id_curso = " . $_SESSION['id_curso'];
                $res = my_query($sql);
                $atualizar_turmas_flag = true;
                if(count($res) > 0) {
                    $atualizar_turmas_flag = false;
                }
                $html .= '
                
                <a class="btn btn-ghost" disabled>'; $html .= ($flag_tem_turmas ? ($atualizar_turmas_flag ? 'Atualizar turmas' : '') : 'Gerar turmas'); $html .= '</a>
                
                ';
            } else {
                $html .= '
                
                <a onclick="
                var flag_tem_turmas = document.getElementById(\'form_atualizar_turmas_dc\').tem_turmas.value;
                
                Swal.fire({
                    title: \'' . $alertTitle . '\',
                    text: \'Tem certeza que deseja ' . $alertText . ' as turmas?\',
                    icon: \'warning\',
                    showCancelButton: true,
                    confirmButtonColor: \'#3085d6\',
                    cancelButtonColor: \'#d33\',
                    confirmButtonText: \'Sim\',
                    cancelButtonText: \'Cancelar\'
                }).then((result) => {    
                    if(result.isConfirmed) {                
                        if(flag_tem_turmas) {
                            
                            document.getElementById(\'form_atualizar_turmas_dc\').submit();
                        } else {
                            Swal.fire({
                                title: \'Gerando turmas\',
                                text: \'Deseja gerar turmas para este ano letivo, ou para o próximo?\',
                                icon: \'info\',
                                showCancelButton: true,
                                confirmButtonColor: \'#3085d6\',
                                cancelButtonColor: \'green\',
                                confirmButtonText: \'Este (' . $ano_letivo . ')\',
                                cancelButtonText: \'Próximo (' . $proximo_ano_letivo . ')\'

                            }).then((result) => {
                                if(result.isConfirmed) {
                                    
                                    document.getElementById(\'form_atualizar_turmas_dc\').ano_letivo.value = \'' . $ano_letivo . '\';
                                    document.getElementById(\'form_atualizar_turmas_dc\').submit();
                                } else {
                                    
                                    document.getElementById(\'form_atualizar_turmas_dc\').ano_letivo.value = \'' . $proximo_ano_letivo . '\';
                                    document.getElementById(\'form_atualizar_turmas_dc\').submit();
                                }
                            });
                        }
                    }

                    
                });
            " class="btn btn-ghost">';
            
            $aletivo = get_proximo_ano_letivo(get_ano_letivo());
            $sql = "SELECT * FROM turma WHERE ano_letivo = '$proximo_ano_letivo' AND id_curso = " . $_SESSION['id_curso'];
            $res = my_query($sql);
            $atualizar_turmas_flag = true;
            if(count($res) > 0) {
                $atualizar_turmas_flag = false;
            }

            $html .= ($flag_tem_turmas ? ($atualizar_turmas_flag ? 'Atualizar turmas' : '') : 'Gerar turmas'); $html .= '</a>
                
                ';
            }
            
            $html .= '
        </form>
    </div>
    <div id="tabela_turmas_diretor_curso" class="overflow-x-auto max-h-96"></div>
    <script>
        function change_select_ano_letivo() {
            var select = document.getElementById("slc_ano_letivo");
            var ano_letivo = select.value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "' . $arrConfig['url_admin'] . 'dashboards/gerar_turmas_ano_letivo.php?' . (isset($_GET['editar']) ? 'editar=true&' : '') . 'ano_letivo=" + ano_letivo, true);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tabela_turmas_diretor_curso").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
        var event = new Event("change");
        var select = document.getElementById("slc_ano_letivo");
        select.dispatchEvent(event);
    </script>
        ';


    return $html;
}

function tabela_vista_professores_turma($dt = false) {    
    global $arrConfig;
    $cont = 0;
    $id_turma = $_GET['id_turma'];    
    $sql = "SELECT DISTINCT view_professor_turno_turma.*, rel_turno_user.id_turno FROM view_professor_turno_turma 
            INNER JOIN rel_turma_user ON rel_turma_user.id_user = view_professor_turno_turma.id_user 
            INNER JOIN rel_turno_user ON rel_turno_user.id_rel_turma_user =  rel_turma_user.id 
            WHERE view_professor_turno_turma.id_turma = $id_turma";
    $res = my_query($sql);  
    $condensado = array();
    /* pr($res);
    die; */
    foreach ($res as $registro) {
        $id_user = $registro['id_user'];

        // Se o registro do usuário já existe no array condensado
        if (isset($condensado[$id_user])) {
            // Adiciona o turno ao campo 'turnos' do registro condensado
            $condensado[$id_user]['turnos'][] = $registro['id_turno'];
        } else {
            // Cria um novo registro condensado para o usuário
            $condensado[$id_user] = $registro;
            // Inicializa o campo 'turnos' com um array contendo o primeiro turno
            $condensado[$id_user]['turnos'] = array($registro['id_turno']);
        }
    }

    // Exibe o resultado

    $res_turnos_condensados = array_values($condensado);                
    /* pr($res_turnos_condensados); */
    $num_turnos = array();
    

    foreach($res_turnos_condensados as $turno) {
        foreach($turno['turnos'] as $id_turno) {
            $sql = "SELECT numero FROM turno WHERE id = " . $id_turno;
            $res = my_query($sql);
            if(count($res) > 0) {
                if($res[0]['numero'] != 0) {
                    $num_turnos[$turno['id_user']][] = $res[0]['numero'];
                }
            }
        }        
    }
    /* pr($num_turnos); */
    
    $html = '
    
    <div class="overflow-x-auto max-h-96">
        <form method="POST" action="' . $arrConfig['url_modules'] . 'trata_editar_turno_user.mod.php' . '">
            <input type="hidden" name="id_turma" value="' . $id_turma . '">
            <input type="hidden" name="cargo" value="professores">
            <div class="">
                <table class="table">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th></th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Disciplinas</th>
                        <th>Turno</th>
                        '; $html .= ($dt ? '<th>Editar</th>' : ''); $html .= '
                    </tr>
                    </thead>
                    <tbody>
                    ';
                    foreach($res_turnos_condensados as $k => $v) {
                        $cont++;
                        $arr_disciplinas = buscar_disciplinas_cargo($_SESSION['id'], 'professor', $_SESSION['id_curso']);
                        /* pr($arr_disciplinas); */
                
                        $sql = "SELECT disciplinas.nome as nome_disciplina FROM disciplinas
                        INNER JOIN rel_disciplina_user ON rel_disciplina_user.id_disciplina = disciplinas.id
                        WHERE rel_disciplina_user.id_user = " . $v['id_user'];
                        $res_disciplinas = my_query($sql);
                        $res_turno = [];
                        foreach($num_turnos as $id_user => $turnos) {
                            if($id_user == $v['id_user']) {
                                $res_turno = ['numero' => implode(', ', $turnos)];
                            }
                        }
                        $html .= '
                        <tr class="hover">
                            <td>' . ($k + 1) . '</td>
                            <td>' . $v['username'] . '</td>
                            <td>' . $v['email'] . '</td>
                            <td>';
                            foreach($arr_disciplinas as $disciplina) {
                                $sql = "SELECT * FROM disciplinas
                                INNER JOIN rel_disciplina_turma ON disciplinas.id = rel_disciplina_turma.id_disciplina
                                WHERE disciplinas.id = " . $disciplina['id_disciplina'] . " AND rel_disciplina_turma.id_turma = " . $id_turma . " AND disciplinas.ativo = 1";
                
                                $res = my_query($sql);
                
                                if(count($res) > 0) {
                                    for($i = 0; $i < count($res); $i++) {
                                        $html .= $res[$i]['abreviatura'] . '<br>';
                                    }
                                } else {
                                    $html .= 'Sem disciplinas<br>';
                                }
                            }
                            $html .= '
                            </td>
                            '; $edicao = (isset($_GET['editar']) ? $_GET['editar'] : ''); $html .= ($dt ? ($edicao ?
                            '<td>
                            ' . gerar_options_turnos($id_turma, $res_turnos_condensados, $v['id_user'], $cont) . '
                            </td>' :
                            '<td>
                            ' . gerar_options_turnos($id_turma,$res_turnos_condensados,$v['id_user'],$cont,true) . '
                            </td>') : '<td>' . (count($res_turno) > 0 ? 'Turno: ' . $res_turno['numero'] : 'Sem turno' ) . '</td>');
                            $html .= ($dt ? ($edicao ? '<td><button class="btn btn-ghost btn-xs" type="submit">Confirmar</button></td>' : '<td><a class="fa fa-edit" href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&editar=true&tab=professores"></a></td>') : ''); $html .= '
                        </tr>                                        
                        ';
                    }
                    $html .= '
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    ';
    return $html;

}

function gerar_options_turnos($id_turma, $turnos_pertence, $id_user, &$cont ,$disabled = false) {    
    $sql = "SELECT DISTINCT id_turno AS id, numero FROM view_turno_turma WHERE numero <> 0 AND id_turma = $id_turma ORDER BY numero ASC";
    $res = my_query($sql);        
    
    // Inicializar array vazio para os turnos do professor
    /* echo $id_user; */
    $arr_turnos = array();
    foreach ($turnos_pertence as $turno) {
        if($id_user == $turno['id_user']) {
            $arr_turnos = $turno['turnos'];
        }
    }    

    /* pr($arr_turnos); */

    
    $html = '<input type="hidden" name="id_user$' . $cont . '" value="' . $id_user . '">';
    
    foreach($res as $turno) {
        // Verificar se o ID do turno está presente nos turnos do professor
        if(in_array($turno['id'] ,$arr_turnos)) {
            // Checkbox marcado se o turno estiver presente
            if(!$disabled) {
                $html .= '
                <div class="form-control">
                    <label class="label cursor-pointer">
                        
                        <span class="label-text">Turno ' . $turno['numero'] .'</span>
                        <input type="checkbox" name="turno_' . $id_user . '%' . $turno['id'] . '" class="checkbox" value="' . $turno['id'] . '" checked></input>
                    </label>
                </div>
                ';
            } else {
                $html .= '
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Turno ' . $turno['numero'] .'</span>                         
                        <input type="checkbox" class="checkbox" value="' . $turno['id'] . '" disabled checked ></input>
                    </label>
                </div>
                ';                            
            }
        } else {
            // Checkbox não marcado se o turno não estiver presente
            if(!$disabled) {
                $html .= '
                <div class="form-control">
                    <label class="label cursor-pointer">
                        
                        <span class="label-text">Turno ' . $turno['numero'] .'</span>                         
                        <input type="checkbox" name="turno_' . $id_user . '%' . $turno['id'] . '" class="checkbox" value="' . $turno['id'] . '" ></input>
                    </label>
                </div>
                ';
            } else {
                $html .= '
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Turno ' . $turno['numero'] .'</span>                         
                        <input type="checkbox" class="checkbox" value="' . $turno['id'] . '" disabled  ></input>
                    </label>
                </div>
                ';                            
            }
        }                
    }
    
    return $html;
}



function tabela_vista_alunos_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];    
    $sql = "SELECT * FROM view_user_curso 
            INNER JOIN rel_turma_user ON rel_turma_user.id_user = view_user_curso.id_user   
            INNER JOIN rel_turno_user ON rel_turno_user.id_rel_turma_user = rel_turma_user.id           
            WHERE cargo = 'aluno' AND id_turma = $id_turma";
    $res = my_query($sql);
    /* pr($res); */
    $html = '
    
    <div class="overflow-x-auto max-h-96">
        <table class="table">
            <!-- head -->
            <thead>
            <tr>
                <th></th>
                <th>Username</th>
                <th>Email</th>
                <th>Turno</th>
                
            </tr>
            </thead>
            <tbody>
            ';

            foreach($res as $k => $v) {
                if($v['id_turno'] != -1) {
                    $sql = "SELECT numero FROM turno WHERE id = " . $v['id_turno'] . "";
                    $res_turno = my_query($sql);
                    $res_turno = array_shift($res_turno);
                } else {
                    $res_turno = ['numero' => 'N/A'];
                }                            

                $html .= '
                <tr class="hover">
                    <td>' . ($k + 1) . '</td>
                    <td>' . $v['username'] . '</td>
                    <td>' . $v['email'] . '</td>
                    <td>Turno: ' . $res_turno['numero'] . '</td>
                </tr>
                ';
            }

            $html .= '            
            </tbody>
        </table>
    </div>
    
    ';
    return $html;
}


function painel_direcao_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    /* gráfico nº atividades por turno */
    $flag_verif_n_atividades_turno = true;
    $labels_atv_turnos = [];
    $data_atv_turnos = [];
    gerar_dados_chart_atividades_turno($id_turma, $labels_atv_turnos, $data_atv_turnos, $flag_verif_n_atividades_turno);
    /* gráfico atividades do turno por mês */
    $flag_verif_n_atividades_turno_turma = true;
    $labels_atv_turnos_mes = [];
    $data_atv_turnos_mes = [];
    gerar_dados_chart_atividades_turno_mes($id_turma, $labels_atv_turnos_mes, $data_atv_turnos_mes, $flag_verif_n_atividades_turno_turma);        
    $condicao = ($flag_verif_n_atividades_turno_turma ? '1==1' : '1==2');        
    /* gráfico de atividades por disciplinas */
    $labels_atv_disciplinas = [];
    $data_atv_disciplinas = [];
    gerar_dados_chart_atividades_disciplinas($id_turma, $labels_atv_disciplinas, $data_atv_disciplinas);
    /* gráfico de esforço semanal por turno */
    $labels_esforco_semanal = [];
    $data_esforco_semanal = [];
    gerar_dados_esforco_semanal_turma($id_turma, $labels_esforco_semanal, $data_esforco_semanal);
    $mes_atual = date('F');
    $porcentagem = 0;
    $total_atividades_mes = 0;
    $texto_atividades_mes = '';
    gerar_dados_total_atividades_mes($id_turma, $porcentagem, $total_atividades_mes, $texto_atividades_mes);
    $atividade_maior_duracao = get_atividade_de_maior_duracao($id_turma);    
    
    
    if(count($atividade_maior_duracao) == 0) {
        $texto_atividade_maior_duracao = '0';
        $descricao_atividade_maior_duracao = 'N/A';
    } else {                
        $texto_atividade_maior_duracao = $atividade_maior_duracao[0]['tempo_sugerido'] . ' horas';
        $descricao_atividade_maior_duracao = $atividade_maior_duracao[0]['titulo'];
    }    
    $mes_atual = date('m');
    $esforco_medio_diario_turma = get_esforco_medio_diario_mes($id_turma, $mes_atual);
    
    $mes_anterior = date('m', strtotime('-1 month'));
    $esforco_medio_diario_turma_mes_anterior = get_esforco_medio_diario_mes($id_turma, $mes_anterior);
    
    
    
    if($esforco_medio_diario_turma_mes_anterior == 0 && $esforco_medio_diario_turma == 0) { // não há atividades no mes anterior e no mês atual
        $texto_porcentagem = 'N/A';
    } else if($esforco_medio_diario_turma_mes_anterior == 0 && $esforco_medio_diario_turma != 0) { // não há atividades no mes anterior e há no atual
        $texto_porcentagem = 'Sem esforço no mês anterior';
    } else if($esforco_medio_diario_turma_mes_anterior != 0 && $esforco_medio_diario_turma != 0) { // há atividades no mes anterior e no atual
        $porcentagem = (($esforco_medio_diario_turma - $esforco_medio_diario_turma_mes_anterior) / $esforco_medio_diario_turma_mes_anterior) * 100;
        if($porcentagem > 0) {
            $texto_porcentagem = abs(round($porcentagem, 2)) . '% a mais que o mês anterior';
        } else {
            $texto_porcentagem = abs(round($porcentagem, 2)) . '% a menos que o mês anterior';
        }

    } else if($esforco_medio_diario_turma_mes_anterior != 0 && $esforco_medio_diario_turma == 0) { // há atividades no mes anterior e não há no atual
        $texto_porcentagem = 'Sem atividades no mês atual';
    }
    
    $correspondencia_mes = [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
    

    $html = '
    <div class=" flex w-1/5 h-1/5 gap-4">        

        <div id="total_atividades_turma" class="">
            <div class="flex flex-col gap-4">                    
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Número de atividades em ' . $correspondencia_mes[$mes_atual] . '</div>
                        <div class="stat-value">' . $total_atividades_mes . '</div>
                        <div class="stat-desc">' . $texto_atividades_mes . '</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Atividade de maior duração</div>
                        <div class="stat-value">' . $texto_atividade_maior_duracao . '</div>
                        <div class="stat-desc">' . $descricao_atividade_maior_duracao . '</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Esforço médio diário em ' . $correspondencia_mes[$mes_atual] . ' </div>
                        <div class="stat-value">' . $esforco_medio_diario_turma . ' h/dia</div>
                        <div class="stat-desc">' . $texto_porcentagem . '</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider lg:divider-horizontal"></div>
        <canvas id="myChart_atv_turno_mes"></canvas>
        <div class="divider lg:divider-horizontal"></div>
        <canvas id="myChart_atv_disciplinas"></canvas>
        <div class="divider lg:divider-horizontal"></div>
        <canvas id="myChart_esforco_semanas"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        var chart_esforco_semanas = document.getElementById("myChart_esforco_semanas").getContext("2d");
        var data_esforco_semanas = {
            labels: ' . json_encode($labels_esforco_semanal) . ',
            datasets: [{       
                label: "Esforço semanal",         
                data: ' . json_encode($data_esforco_semanal) . ',
                backgroundColor: [
                    "rgba(255, 99, 132, 0.2)",
                    "rgba(54, 162, 235, 0.2)",
                    "rgba(255, 206, 86, 0.2)",
                    "rgba(75, 192, 192, 0.2)",
                    "rgba(153, 102, 255, 0.2)",
                    "rgba(255, 159, 64, 0.2)"
                ],
                borderColor: [
                    "rgba(255, 99, 132, 1)",
                    "rgba(54, 162, 235, 1)",
                    "rgba(255, 206, 86, 1)",
                    "rgba(75, 192, 192, 1)",
                    "rgba(153, 102, 255, 1)",
                    "rgba(255, 159, 64, 1)"
                ],
                borderWidth: 1
            }]
        };
        const config_esforco_semanal = {
            type: \'bar\',
            data: data_esforco_semanas,
            options: {
              responsive: true,
              plugins: {
                legend: {
                    position: \'top\',
                },
                title: {
                    display: true,
                    text: \'Esforço nas próximas semanas (horas)\'
                }
            },
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            },            
        };
        new Chart(chart_esforco_semanas, config_esforco_semanal);

        var bar_disciplinas = document.getElementById("myChart_atv_disciplinas").getContext("2d");        
        var data_disciplinas = {
            labels: ' . json_encode($labels_atv_disciplinas) . ',
            
            datasets: [{
                label: "Total de atividades",
                data: ' . json_encode($data_atv_disciplinas) . ',
                backgroundColor: [
                    "rgba(255, 99, 132, 0.2)",
                    "rgba(54, 162, 235, 0.2)",
                    "rgba(255, 206, 86, 0.2)",
                    "rgba(75, 192, 192, 0.2)",
                    "rgba(153, 102, 255, 0.2)",
                    "rgba(255, 159, 64, 0.2)"
                ],
                borderColor: [
                    "rgba(255, 99, 132, 1)",
                    "rgba(54, 162, 235, 1)",
                    "rgba(255, 206, 86, 1)",
                    "rgba(75, 192, 192, 1)",
                    "rgba(153, 102, 255, 1)",
                    "rgba(255, 159, 64, 1)"
                ],
                borderWidth: 1
            }]
        };
        const config_disciplinas = {
            type: \'pie\',
            data: data_disciplinas,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: \'top\',
                    },
                    title: {
                        display: true,
                        text: \'Total de atividades/disciplinas/mês\'
                    }
                }
            },
        };
        new Chart(bar_disciplinas, config_disciplinas);
                        
        var bar_turno_mes = document.getElementById("myChart_atv_turno_mes").getContext("2d");
        var labels = ' . json_encode($labels_atv_turnos_mes) . ';
        var data = ' . json_encode($data_atv_turnos_mes) . ';
        var datasets = [];
        
        var colors = [
            "rgba(255, 99, 132, 0.2)",
            "rgba(54, 162, 235, 0.2)",
            "rgba(255, 206, 86, 0.2)",
            "rgba(75, 192, 192, 0.2)",
            "rgba(153, 102, 255, 0.2)",
            "rgba(255, 159, 64, 0.2)"
        ];

        var borderColors = [
            "rgba(255, 99, 132, 1)",
            "rgba(54, 162, 235, 1)",
            "rgba(255, 206, 86, 1)",
            "rgba(75, 192, 192, 1)",
            "rgba(153, 102, 255, 1)",
            "rgba(255, 159, 64, 1)"
        ];

        var i = 0;
        if(' . $condicao . ') {
            for (var turno in data) {
                // tentar converter o turno em numero
                var aux = parseInt(turno);
                
                var label;
                if(isNaN(aux)) {
                    label = "Todos";
                } else {
                    label = "Turno " + turno;
                }
                
                datasets.push({
                    label: label,
                    data: data[turno],
                    backgroundColor: colors[i % colors.length],
                    borderColor: borderColors[i % borderColors.length],
                    borderWidth: 1
                });
                i++;
            }

        } else {
            datasets.push({
                label: "Turma toda",
                data: data,
                backgroundColor: colors[0],
                borderColor: borderColors[0],
                borderWidth: 1
            });
        }


        var data_turno_mes = {
            labels: labels,
            datasets: datasets
        };

        const config_turno_mes = {
            type: \'line\',
            data: data_turno_mes,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: \'top\',
                    },
                    title: {
                        display: true,
                        text: \'Atividades por mês ' . ($condicao ? '' : 'por turno') . '\'
                    }
                }
            },
        };
        new Chart(bar_turno_mes, config_turno_mes);


        

    </script>

    ';

    return $html;
}


function tabela_disciplinas_instituicao() {
    global $arrConfig;
    
    $sql = "SELECT * FROM rel_instituicao_disciplinas
            INNER JOIN disciplinas ON disciplinas.id = rel_instituicao_disciplinas.id_disc
            WHERE id_instituicao = " . $_SESSION['id_instituicao'];
    $res = my_query($sql);
    /* pr($res);
    die; */
    $html = '
    <div class="flex justify-around">
        <div class="w-auto text-center pt-5">
            <h2 class=" text-lg mb-4 ">Adicionar disciplinas a instituição</h2>
            <form method="post" action="' . $arrConfig['url_modules'] . 'trata_adicionar_disciplina_instituicao.mod.php" id="disciplinas_form">
                <div class="flex mb-4 gap-2">                                            
                    <input type="text" name="nome_disciplina" id="disciplinas-input" placeholder="Nome da disciplina" class="input input-bordered">                    
                    <button type="button" id="add-disciplinas" class="btn btn-ghost text-xs py-1 px-2">
                        Adicionar
                    </button>
                    <button type="submit" class="btn btn-ghost text-xs py-1 px-2">
                        Submeter
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <div id="disciplinas-list" class="mb-4 h-56">
                        <!-- Os itens de e-mail serão inseridos aqui -->
                    
                    </div>
                </div>
            </form>
        </div>
        
        <div class="overflow-x-auto max-h-72">
            
            <table class="table h-56">
                <!-- head -->
                <thead>
                <tr>
                    <th></th>
                    <th>Nome</th>                
                    <th>Remover</th>
                </tr>
                </thead>
                <tbody>
                ';
                $cont = 0;
                foreach($res as $disciplina) {
                    $cont++;
                    $html .= '
                    <tr class="hover">
                        <td>' . $cont . '</td>
                        <td>' . $disciplina['nome'] . '</td>                    
                        <td><a data-id="' . $disciplina['id'] . '" style="cursor: pointer;" class="fa fa-trash remove-disciplina"></a></td>
                    </tr>
                    ';
                }
                $html .= '            
                </tbody>
            </table>
        </div>
    </div>
    <script>
                
        $(document).ready(function() {
            
            $(\'.remove-disciplina\').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Tem a certeza?",
                    text: "Esta ação é irreversível",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sim, remover",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {                                
                        var id = $(this).data(\'id\');        
                        $.ajax({
                            url: "' . $arrConfig['url_modules'] . 'trata_remover_disciplina_instituicao.mod.php",
                            type: \'GET\',
                            data: { id_disciplina: id },
                            success: function(result) {
                                console.log(result);
                                // Atualize a página ou faça algo com o resultado
                                /* location.reload(); */
                            }
                        });
                    }
                });
                        
            });
        });

        document.addEventListener(\'DOMContentLoaded\', function() {                                                
            let insertedValues = [];
            document.getElementById("disciplinas-input").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); 
                    document.getElementById("add-disciplinas").click();
                    
                }
            });

            document.getElementById("add-disciplinas").onclick = function() {    
                console.log("aqui");            
                var disciplinaInput = document.getElementById("disciplinas-input");
                var disciplinaList = document.getElementById("disciplinas-list");
                var disciplinaId = disciplinaInput.value.trim();
                var disciplinaText = disciplinaInput.value;
                
                
                

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
    if(isset($_SESSION['msg_erro'])) {
        $html .= '
        <script>
            Swal.fire({
                title: "Erro",
                text: "' . $_SESSION['msg_erro'] . '",
                icon: "error",
                timer: 2000,
                
            })
        </script>
        ';
        unset($_SESSION['msg_erro']);
    }
    return $html;
}

function tabela_diretores_curso_instituicao() {
    global $arrConfig;
    $sql = "SELECT * FROM users 
    INNER JOIN curso ON curso.id_diretor_curso = users.id 
    INNER JOIN rel_instituicao_curso ON rel_instituicao_curso.id_curso = curso.id
    WHERE rel_instituicao_curso.id_instituicao = " . $_SESSION['id_instituicao'] . "";
    $res = my_query($sql);
    $cont = 0;
    /* pr($res); */
    $html = '
    
    <div class="overflow-x-auto">
        <form method="POST" action="' . $arrConfig['url_modules'] . 'trata_editar_turno_user.mod.php' . '">
            <input type="hidden" name="id_instituicao" value="' . $_SESSION['id_instituicao'] . '"> 
            
            <table class="table">
                <!-- head -->
                <thead>
                <tr>
                    <th></th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Curso</th>                    
                    
                </tr>
                </thead>
                <tbody>
                ';    
                foreach($res as $k => $v) {
                    $cont++;                    
                    $html .= '
                    <tr class="hover">
                        <td>' . ($k + 1) . '</td>
                        <td>' . $v['username'] . '</td>
                        <td>' . $v['email'] . '</td>
                        <td>' . $v['nome_curso'] . '</td>
                        
                    </tr>
                    ';                                    
                }
                $html .= '
                </tbody>
            </table>
        </form>
    </div>
    ';
    return $html;
}

function tabela_cursos_instituicao() {
    global $arrConfig;
    $editar = (isset($_GET['editar']) ? $_GET['editar'] : false);
    
    $sql = "SELECT * FROM curso 
            INNER JOIN rel_instituicao_curso ON rel_instituicao_curso.id_curso = curso.id 
            WHERE rel_instituicao_curso.id_instituicao = " . $_SESSION['id_instituicao'] . "";
    $res = my_query($sql);
    $cont = 0;
    
    if($editar) {        
        $sql = "SELECT * FROM curso WHERE id = " . $_GET['id_curso'];        
        $res_editar = my_query($sql);
        $res_editar = array_shift($res_editar);        
        if(isset($res_editar)) {
            if($res_editar['id_diretor_curso'] != -1) {
                
                $sql = "SELECT email FROM users WHERE id = " . $res_editar['id_diretor_curso'];
                $res_email = my_query($sql);
                $res_editar['email'] = $res_email[0]['email'];
            } else {
                $res_editar['email'] = '';
            }
        } else {
            $editar = false;        
        }
    }
    
    $html = '
    
    <div class="overflow-x-auto">
        
        <div class="flex flex-row">
        <form id="form_criar_editar_curso" method="post" class="w-4/12" action="' . $arrConfig['url_modules'] . 'trata_editar_curso.mod.php' . ($editar ? '?tipo=editar' : '?tipo=criar' ) . '" class="overflow-x-auto">
        
            <div class="flex flex-col gap-6 ml-8">
            <h1 class="text-xl text-center font-bold">Criar curso</h1>
            ' . ($editar ? '<input type="hidden" name="id_curso" value="' . $_GET['id_curso'] . '" />' : '') . '
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Nome do curso</span>
                        </div>
                        <input name="nome_curso" required type="text" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" value="' . ($editar ? $res_editar['nome_curso'] : '') . '" />
                    </label>
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Abreviatura</span>
                        </div>
                        <input name="abreviatura" required type="text" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" value="' . ($editar ? $res_editar['abreviatura'] : '') . '"/>
                    </label>
                </div>
                <div class="flex flex-row gap-8">
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Duração (anos)</span>
                        </div>
                        <input name="duracao" required type="number" min="0" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" value="' . ($editar ? $res_editar['duracao'] : '') . '"/>
                    </label>
                    <label class="form-control w-full max-w-xs">
                        <div class="label">
                            <span class="label-text">Diretor de curso</span>
                        </div>
                        <input name="diretor_curso" required type="email" placeholder="Escreva aqui." class="input input-bordered w-full max-w-xs" value="' . ($editar ? $res_editar['email'] : '') . '"/>
                    </label>
                </div>                                                                                    
                <label class="form-control mt-auto w-full">        
                    <button id="submeter_form_curso" class="btn w-full">' . ($editar ? 'Editar' : 'Criar') . '</button>
                </label>
                
                ';                
                if(isset($_SESSION['msg_erro'])) {
                    $html .= '<script>Swal.fire({
                        title: "Erro",
                        text: "' . $_SESSION['msg_erro'] . '",
                        icon: "error",
                        timer: 2000,
                        
                    })</script>';
                    unset($_SESSION['msg_erro']);
                }
                $html .= '
                
                
                
            </div>
        </form>
        <script>
            form = document.getElementById("form_criar_editar_curso");
            form.addEventListener("submit", function(e) {
                document.getElementById(\'loading-overlay\').classList.remove(\'hidden\');
            });
        </script>
    
    <div class="divider lg:divider-horizontal"></div>
            '; 
            if(count($res) > 0) {
               $html .= '
            <form method="POST" action="' . $arrConfig['url_modules'] . 'trata_editar_turno_user.mod.php' . '">
                <input type="hidden" name="id_instituicao" value="' . $_SESSION['id_instituicao'] . '">
            
                <table class="table">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th></th>
                        <th>Nome curso</th>
                        <th>Abreviatura</th>
                        <th>Duração</th>
                        <th>Estado</th>
                        <th>Editar</th>
                        <th>Remover</th>

                    </tr>
                    </thead>
                    <tbody>
                    ';
                    foreach($res as $k => $v) {
                        
                        $cont++;
                        $html .= '
                        <tr class="hover">
                            <td>' . ($k + 1) . '</td>
                            <td>' . $v['nome_curso'] . '</td>
                            <td>' . $v['abreviatura'] . '</td>
                            <td>' . $v['duracao'] . '</td>
                            <td>' . ($v['ativo'] == 1 ? 'Ativo' : 'Pendente') . '</td>
                            <td><a class="fa fa-edit" href="' . $arrConfig['url_admin'] . 'instituicao.php?editar=true&id_curso=' . $v['id_curso'] . '"></a></td>
                            <td><a style="cursor:pointer;" class="fa fa-trash" onClick="Swal.fire({
                                title: \'Tem certeza?\',
                                text: \'Você não poderá reverter isso!\',
                                icon: \'warning\',
                                showCancelButton: true,
                                confirmButtonColor: \'#3085d6\',
                                cancelButtonColor: \'#d33\',
                                confirmButtonText: \'Sim, remover!\',
                                cancelButtonText: \'Cancelar\'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = \'' . $arrConfig['url_modules'] . 'trata_remover_curso_instituicao.mod.php?id_curso=' . $v['id_curso'] . '\';
                                }
                            })"></a></td>
                            
                        </tr>
                        ';
                    }
                    $html .= '
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    ';
            } else {
                $html .= '
                <div class="flex justify-center">
                    <span class="text-center text-gray-500">Ainda não há cursos associados a esta instituição.</span>
                </div>
                ';
            }
            
    return $html;
}

function ai_assistente_aluno() {
    global $arrConfig;
    $rand = rand(1, 100000);
    $id_user = $_SESSION['id'];
    $id_turma = $_GET['id_turma'];
    $dotenv = Dotenv\Dotenv::createImmutable($arrConfig['dir_site']);
    $dotenv->load();
    $codigo_unico = $_ENV['CHAVE_ENVIAR'];
    $html = '
    
    <div class="flex gap-4">
        <div class="w-6/12 mx-auto p-4">
            <div class="bg-base-200 rounded-lg shadow p-4">
                <div class="chat-container space-y-4 mb-4 max-h-96 overflow-y-auto" id="chatContainer">
                    <div class="chat chat-start">
                        <div class="chat-image avatar">
                            <div class="w-10 rounded-full">
                                <img alt="Obi-Wan Kenobi" src="' . $arrConfig['url_public'] . '/eve.png' . '" />
                            </div>
                        </div>
                        <div class="chat-header">
                            Educse I.A.
                            <time class="text-xs opacity-50"></time> <!-- hora da mensagem -->
                        </div>
                        <div class="chat-bubble">Olá, como posso ajudar te hoje?</div>
                        <br>             
                    </div>
                </div>
                <form id="chatForm" class="flex flex-col gap-3">
                    <div id="caixa-sugestoes" class="flex justify-around ">
                        <div onClick="submeterSugestao(\'Quais as próximas atividades?\')" class="badge badge-md bg-base-300 cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-translate-y-2">Quais as próximas atividades?</div>
                        <div onClick="submeterSugestao(\'Com base nas minhas próximas atividades, organize meu calendário de uma maneira agradável.\')" class="badge badge-md bg-base-300 cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-translate-y-2">Organize meu calendário</div>
                        <div onClick="submeterSugestao(\'Qual a minha disciplina com mais atividades?\')" class="badge badge-md bg-base-300 cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-translate-y-2">Qual a disciplina com mais atividades?</div>
                    </div>
                    <div class="flex">
                        <input type="text" id="chatInput" class="input input-bordered w-full max-w-full" placeholder="Escreva sua mensagem..." autocomplete="off" required>
                        <button type="submit" id="submitFormBtn" class="btn bg-base-300"><i class="fas fa-solid fa-arrow-up"></i></button>
                    </div>
                </form>
            </div>
            <span class="text-xs text-center text-gray-500 block mt-4">As funções associadas ao assistente A.I. estão em fase Beta, e são sucetíveis a erros.</span>
        </div>
        <div class="w-6/12">
            <div id="render-calendar-here-ai">
                <div id="ec' . $rand . '"></div>
            </div>
        </div>
    </div>

    <script>
    let eventos_carregados;
    function submeterSugestao(texto) {        
        document.getElementById(\'chatInput\').value = texto;
        document.getElementById(\'submitFormBtn\').click();
    }
    
    
    let options = {
        view: \'timeGridWeek\',
        
        
        
        customButtons: {
            vista: {
                text: \'Vista\',
                click: function() {
                    if (ec.view.type === \'timeGridWeek\') {
                        ec.changeView(\'timeGridDay\');
                    } else {
                        ec.changeView(\'timeGridWeek\');
                    }
                }
            },
            removerEventos: {
                text: \'Remover eventos\',
                click: function(info) {
                    Swal.fire({
                        title: \'Tem certeza?\',
                        text: "Você não poderá reverter isso!",
                        icon: \'warning\',
                        showCancelButton: true,
                        confirmButtonColor: \'#3085d6\',
                        cancelButtonColor: \'#d33\',
                        confirmButtonText: \'Sim, remover!\',
                        cancelButtonText: \'Cancelar\'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            ec.destroy();
                            createCalendar();
                            $.ajax({
                                url: "' . $arrConfig['url_admin'] . 'dashboards/apagar_eventos_aluno.php",
                                type: "DELETE",                                
                                success: function(response) { 
                                    // este código será executado se a requisição for bem-sucedida
                                    Swal.fire(
                                        \'Removido!\',
                                        \'Os eventos foram removidos.\',
                                        \'success\'
                                    )
                                }
                            })                            
                        }
                    })
                    
                }
            },
            guardarEventos: {
                text: \'Guardar eventos\',
                click: function() {
                    
                }
            }
        },
        allDaySlot: false,
        
        headerToolbar: {start: \'removerEventos\', center: \'\', end: \'listWeek timeGridWeek today, prev next\'},
        buttonText: {
            today: \'Hoje\',
            month: \'Mês\',
            week: \'Semana\',
            day: \'Dia\',
            list: \'Lista\',
            listWeek: \'Lista\',
            timeGridWeek: \'Semana\',
        },
    }
    

    let ec;
    function createCalendar() {
        ec = new EventCalendar(document.getElementById(\'ec' . $rand . '\'), options);
    }
    createCalendar();
    $.ajax({
        url: "' . $arrConfig['url_admin'] . 'dashboards/get_eventos_aluno.php",
        type: "GET",        
        success: function(response) {
            // este código será executado se a requisição for bem-sucedida
            eventos_carregados = JSON.parse(response);    
            for (let evento of eventos_carregados) {
                ec.addEvent(evento);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // este código será executado se ocorrer um erro na requisição
            console.log(\'Erro ao carregar eventos:\', errorThrown);
        }
    })

    document.getElementById(\'chatForm\').addEventListener(\'submit\', function(event) {
        event.preventDefault();
        const caixa_sugestoes = document.getElementById(\'caixa-sugestoes\');
        caixa_sugestoes.style.display = \'none\';
        const chatContainer = document.getElementById(\'chatContainer\');
        const chatInput = document.getElementById(\'chatInput\');
        const message = chatInput.value.trim();
        const currentTime = new Date().toLocaleTimeString([], { hour: \'2-digit\', minute: \'2-digit\' });

        if (message !== \'\') {
            const chatMessage = `
                <div class="chat chat-end">
                    <div class="chat-image avatar">
                        <div class="w-10 rounded-full">
                            <img alt="User" src="' . $arrConfig['url_pfp'] . 'e.png' . '" />
                        </div>
                    </div>
                    <div class="chat-header">
                        Você
                        <time class="text-xs opacity-50">${currentTime}</time>
                    </div>
                    <div class="chat-bubble bg-primary">${message}</div>                        
                </div>
            `;
            chatContainer.insertAdjacentHTML(\'beforeend\', chatMessage);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            chatInput.value = \'\';
        }
        chatContainer.scrollTop = chatContainer.scrollHeight;
        const mensagem_carregando = `
        <div id="mensagem-carregando">
            <div class="chat chat-start">
                <div class="chat-image avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Obi-Wan Kenobi" src="' . $arrConfig['url_public'] . '/eve.png' . '" />
                    </div>
                </div>
                <div class="chat-header">
                    Educse I.A.
                    <time class="text-xs opacity-50">Carregando</time> <!-- hora da mensagem -->
                </div>
                <div class="chat-bubble"><span class="loading loading-dots loading-sm"></span></div>
            </div>
        </div>
        `;
        chatContainer.insertAdjacentHTML(\'beforeend\', mensagem_carregando);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        chatInput.value = \'\';
        
        xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "' . $arrConfig['url_admin'] . '/dashboards/' . 'chamar_api_openai.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {

            if (this.readyState == 4 && this.status == 200) {
                // A solicitação foi bem-sucedida, você pode processar a resposta aqui
                let flagEventos = false;
                let resposta = this.responseText;
                let jsonString = \'\';
                let eventosJSON = \'\';
                
                if(resposta.includes(\'$$eventos$$\')) {
                    flagEventos = true;
                    console.log("aqui");
                    const eventosPos = resposta.indexOf(\'$$eventos$$\');

                    // Verificar se $$eventos$$ foi encontrado na string
                    if (eventosPos !== -1) {

                        // Extrair o JSON que está após $$eventos$$
                        let stringEventos = \'$$eventos$$\';
                        let eventosStr = resposta.substring(eventosPos + stringEventos.length).trim();

                        eventosStr = eventosStr.replace(/\n\d+\.\s+/g, \'\').replace(/\n/g, \'\');
                        eventosStr = \'[\' + eventosStr.replace(/}{/g, \'},{\') + \']\';
                        try {
                            const eventos = JSON.parse(eventosStr);
                            for (let evento of eventos) {
                                ec.addEvent(evento);                                
                            }
                            $.ajax({
                                url: "' . $arrConfig['url_modules'] . 'trata_adicionar_eventos_ai.mod.php",
                                type: "POST",
                                data: { eventos: JSON.stringify(eventos) },
                                success: function(response) {
                                    // este código será executado se a requisição for bem-sucedida
                                    console.log(response);
                                    console.log(\'Eventos salvos com sucesso\');
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    // este código será executado se ocorrer um erro na requisição
                                    console.log(\'Erro ao salvar eventos:\', errorThrown);
                                }
                            })
                        } catch (e) {
                            console.log("Erro ao analisar JSON:", e.message);
                        }
                        
                    } else {
                        console.log("Tag $$eventos$$ não encontrada na string.");
                    }
                    console.log(resposta)
                    resposta = resposta.split(\'$$eventos$$\')[0].trim();    
                    resposta = resposta.replace(\'$$eventos$$\', \'\');
                }
                console.log(flagEventos);

                console.log(resposta);
                
                console.log(this.responseText);
                chatContainer.removeChild(document.getElementById(\'mensagem-carregando\'));
                var decodedResponse = decodeURIComponent(this.responseText);
                console.log(decodedResponse);
                const tempo_resposta = new Date().toLocaleTimeString([], { hour: \'2-digit\', minute: \'2-digit\' });
                const chatMessage = `
                
                    <div class="chat chat-start">
                    <div class="chat-image avatar">
                        <div class="w-10 rounded-full">
                            <img alt="Obi-Wan Kenobi" src="' . $arrConfig['url_public'] . '/eve.png' . '" />
                        </div>
                    </div>
                    <div class="chat-header">
                        Educse I.A.
                        <time class="text-xs opacity-50">${tempo_resposta}</time> <!-- hora da mensagem -->
                    </div>
                        <div class="chat-bubble">${resposta}</div>
                    </div>
                `;
                chatContainer.insertAdjacentHTML(\'beforeend\', chatMessage);
                chatContainer.scrollTop = chatContainer.scrollHeight;
                chatInput.value = \'\';
            }
        };
        xmlhttp.send("message=" + encodeURIComponent(message) + "&outro=' . $codigo_unico . '" + "&user=' . $_SESSION['id'] . '" + "&id_turma=' . $id_turma . '");

    });
    
    </script>
        

    ';
    return $html;
}