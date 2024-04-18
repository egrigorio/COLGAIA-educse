<?php
include 'config.inc.php';
include 'dados.inc.php';

function pr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function logs() {
    global $tipoLog;
    $arrUrl = explode('/', $_SERVER['REQUEST_URI']);
    $file = $arrUrl[count($arrUrl) - 1];

    switch($file) {
        case 'tratalogin.inc.php':
            $_SESSION['tipoLog'] = 'Login';
            break;
        case 'logout.php': 
            $_SESSION['tipoLog'] = 'Logout';
            break;
        default: 
            $_SESSION['tipoLog'] = '';
            
    };

    $dataHora = date('Y-m-d H:i:s');
    $url = $_SERVER['REQUEST_URI'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $idUser = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

    $session = session_id();
    
    $sql = "INSERT INTO logs (dataHora, url, ip, idUser, session, tipoLog) VALUES ('$dataHora', '$url', '$ip', $idUser, '$session', '$tipoLog')";
    $result = my_query($sql);

};

function gerar_formulario($tipo, $modulo, $action = null, $pkey = null, $pkey_valor = null) {
    global $arrConfig;

    if(!$action) $action = $arrConfig['url_modules'] . 'trata_' . $tipo . '.mod.php?modulo=' . $modulo;

    echo "<h1>$tipo $modulo</h1>";
    echo '<div>';
    $flagEditarInserir = false;
    $arrCampos = array();    
    $tipo == 'editar' ? $sql = "SELECT * FROM $modulo WHERE $pkey = " . $pkey_valor : '';
    isset($sql) ? $arrResultado = my_query($sql) : '';
    
    def_arrCampos($modulo, $arrCampos);
    echo '<form action="' . $action . '" method="post" enctype="multipart/form-data">';
    
    foreach($arrCampos as $kCampos => $vCampos) {
        switch($tipo) {
            case 'editar':                
                campos_formulario($vCampos, $kCampos, $tipo, $flagEditarInserir, $arrResultado, $pkey);
                break;
            case 'criar':
                $tipo = 'inserir';
            case 'inserir':                
                campos_formulario($vCampos, $kCampos, $tipo,$flagEditarInserir);
                break;
            
        }
        
    };
    echo $flagEditarInserir ? '<input type="submit" value="Enviar">' : '<h1>Não é possível editar ou inserir</h1>';
    echo '</form>';
    echo '<button><a href="' . $arrConfig['url_site'] . '/admin/paginas/' . $modulo . '.adm.php">Voltar</a></button>';
    echo '</div>';
}

function campos_formulario($vCampos, $kCampos , $crud ,&$flagEditarInserir,$arrResultados = null, $chave = null ) {
    global $arrConfig;    

    if($vCampos['chave'] && $crud === "editar" && !$vCampos['editar']) echo '<input type="hidden" name="' . $chave . '" value="' . $arrResultados[0][$chave] . '">';
    if($vCampos[$crud]) {
        $flagEditarInserir = true;                
        switch($vCampos['tipo']) {
            case 'float': 
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo '<input type="number" min=0 step="0.01" name="' . $kCampos . '" value="' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '"><br>';
                break;
            case 'int':
                
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo $kCampos == 'ativo' ? '<input type="number" max=1 min=0 name="' . $kCampos . '" value="' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '"><br>' : '<input type="number" name="' . $kCampos . '" value="' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '><br>';;
                break;
            case 'varchar':
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo '<input type="text" name="' . $kCampos . '" value="' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '><br>';
                /* echo '<input type="hidden" name="current_' . $kCampos . '" value="' . $arrResultados[0][$kCampos] . '">'; */
                break;
            case 'img':                 
                if($crud == 'editar') {
                    echo '<label for="atual">Imagem atual</label>';
                    echo '<img name="atual" style="max-width:60px;" src="' . $arrConfig['url_img'] . $arrResultados[0][$kCampos] . '"><br>';
                    echo '<input type="hidden" name="current_' . $kCampos . '" value="' . $arrResultados[0][$kCampos] . '">';
                }
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label><br>    ';
                echo '<input type="file" accept="' . $vCampos['formatos_aceites'] . '" name="' . $kCampos . '" ' . ($vCampos['required'] && $crud == 'editar' ? '' : ($vCampos['required'] && $crud == 'inserir' ? 'required' : '')) . '><br>';
                break;
            case 'checkbox':
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo '<input type="checkbox" name="' . $kCampos . '" checked="' . ($arrResultados ? $arrResultados[0][$kCampos] ? 'true' : 'false' : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '"><br>';
                break;
            case 'textarea':
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo '<textarea name="' . $kCampos . '" ' . ($vCampos['required'] ? 'required' : '') . '>' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '</textarea><br>';
                break;
            case 'datetime':
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo '<input type="datetime-local" name="' . $kCampos . '" value="' . ($arrResultados ? $arrResultados[0][$kCampos] : '') . '" ' . ($vCampos['required'] ? 'required' : '') . '><br>';
                break;
            case 'escondido':
                echo '<input type="hidden" name="' . $kCampos . '" value="' . $vCampos['default'] . '">';
                break;
            case 'select':
                echo '<label for="' . $kCampos . '">' . $vCampos['legenda'] . '</label>    ';
                echo "<td>";
                echo "<select name='$kCampos'>";
                // carregar de OPÇÕES pré-definidas
                if(isset($vCampos['opcoes'])) {
                    foreach($vCampos['opcoes'] as $k => $v) {
                    $selected = '';
                    if(isset($vCampos['default'])) {
                        if($vCampos['default'] == $k) {
                        $selected = 'selected="selected"';
                        }
                    }
                    echo "<option value='$k' $selected>$v</option>";
                    }
                // carregar de uma tabela da BD
                } elseif(isset($vCampos['carrega_opcoes'])) {
                    $where = '';
                    if(isset($vCampos['carrega_opcoes']['ativo'])) {
                    $ativo = $vCampos['carrega_opcoes']['ativo'];
                    $where = " $ativo = '1'";
                    }
                    $tabela = $vCampos['carrega_opcoes']['tabela'];
                    $query = "SELECT * FROM $tabela WHERE 1=1 AND $where";
                    $arrResultados = my_query($query);
                    if(isset($vCampos['carrega_opcoes']['null'])) {
                    $null_legenda = isset($vCampos['carrega_opcoes']['null_legenda']) ? $vCampos['carrega_opcoes']['null_legenda'] : 'Seleccione uma opção';
                    $null_valor = isset($vCampos['carrega_opcoes']['null_valor']) ? $vCampos['carrega_opcoes']['null_valor'] : '';
                    echo "<option value='$null_valor'>$null_legenda</option>";
                    }
                    foreach($arrResultados as $k => $v) {
                    $selected = '';
                    if(isset($vCampos['default'])) {
                        if($vCampos['default'] == $v[$vCampos['carrega_opcoes']['chave']]) {
                        $selected = 'selected="selected"';
                        }
                    }
                    $id = $v[$vCampos['carrega_opcoes']['chave']];
                    $legenda = $v[$vCampos['carrega_opcoes']['legenda']];
                    echo "<option value='$id' $selected>$legenda</option>";
                    }
                }
                echo "</select>";
                echo "</td><br>";
                break;
        }
    }
    
};

function definir_redirecionamento($modulo) {

    $base = '/admin';
    switch ($modulo) {
        case 'turma': 
            /* adicionar aqui uma validação para ver se o diretor de turma em questão tem autorização para criar turma naquele curso, se não tiver 
            não posso deixar criar turma */
            $sql = "SELECT * FROM turma ORDER BY id DESC LIMIT 1"; 
            $res = my_query($sql);
            $sql = "SELECT * FROM curso WHERE id = " . $res[0]['id_curso'];
            $res2 = my_query($sql);
            $id = $res[0]['id'];
            if(!($res[0]['id_diretor_turma'] == $res2['id_diretor_curso'])) {
                $sql = "INSERT INTO rel_user_turma (id_turma, id_user) VALUES ($id, " . $res2[0]['id_diretor_curso'] . ")";
                my_query($sql);                
            }
            $redirect = $base . "/turma.php?id_turma=$id";
            $sql = "INSERT INTO rel_turma_user (id_turma, id_user) VALUES ($id, " . $_SESSION['id'] . ")";

            my_query($sql);
            return $redirect;
            break;
        case 'curso':
            $sql = "SELECT id FROM curso ORDER BY id DESC LIMIT 1"; 
            $res = my_query($sql);
            $id = $res[0]['id'];
            $redirect = $base . "/curso.php?id_curso=$id";
            $sql = "INSERT INTO rel_user_curso (id_curso, id_user) VALUES ($id, " . $_SESSION['id'] . ")";
            return $redirect;
            break;
        
    }

}

function buscar_cursos_diretor($id_diretor) {
    $sql = "SELECT * FROM curso WHERE id_diretor_curso = ($id_diretor) ";
    return my_query($sql);
}

function buscar_turmas_diretor($id_diretor, &$arr_turmas) {
    
    foreach($arr_turmas as &$item) {
        if($item['id_diretor_turma'] == $id_diretor) {                
            $item['nome_turma'] = $item['nome_turma'] . ' (diretor)';    
        }
    }    
    return $arr_turmas;
}

function buscar_direcao_turma($id_diretor) {
    $sql = "SELECT nome_turma FROM turma WHERE id_diretor_turma = ($id_diretor) ";
    return my_query($sql);
}

function buscar_disciplinas_cargo($id_user, $cargo, $id_curso) {
    $sql = "SELECT * FROM rel_disciplina_user WHERE id_user = ($id_user) AND cargo = '$cargo' AND id_curso = $id_curso";
    return my_query($sql);

}

function buscar_disciplinas_curso($id_curso, $tipo) {
    if($tipo == 1) {
        $sql = "SELECT * FROM rel_disciplina_curso WHERE id_curso = ($id_curso) ";
        return my_query($sql);
    } else {
        $sql = "SELECT * FROM rel_disciplina_curso WHERE id_curso = ($id_curso) ";
        $res = my_query($sql);
        $arr_disciplinas = array();
        foreach($res as $k => $v) {
            $sql = "SELECT * FROM disciplinas WHERE id = " . $v['id_disciplina'];
            $res2 = my_query($sql);
            $arr_disciplinas = array_merge($arr_disciplinas, $res2);
        }
        return $arr_disciplinas;

    }

}

function get_ano_letivo() {
    $mes_atual = date("n"); // obtém o mês atual
    $ano_atual = date("Y"); // obtém o ano atual
    if ($mes_atual >= 9) {
        // Se o mês atual for de setembro a dezembro, o ano letivo é o ano atual e o próximo ano
        $ano_letivo = $ano_atual . "/" . substr($ano_atual + 1, 2);
    } else {
        // Se o mês atual for de janeiro a junho, o ano letivo é o ano anterior e o ano atual
        $ano_letivo = ($ano_atual - 1) . "/" . substr($ano_atual, 2);
    }
    return $ano_letivo;
}

function get_proximo_ano_letivo($ano_letivo) {
    $anos = explode('/', $ano_letivo); // divide a string do ano letivo em dois anos
    $ano1 = $anos[0]; // pega o primeiro ano completo
    $ano2 = '20' . $anos[1]; // adiciona '20' ao início do segundo ano para obter o ano completo

    $proximo_ano1 = $ano1 + 1; // incrementa o primeiro ano
    $proximo_ano2 = $ano2 + 1; // incrementa o segundo ano

    $proximo_ano_letivo = $proximo_ano1 . '/' . substr($proximo_ano2, 2); // junta os anos incrementados em uma string
    return $proximo_ano_letivo;
}

function buscar_turmas_curso($id_curso) {
    $sql = "SELECT * FROM turma WHERE id_curso = ($id_curso) ";
    $res = my_query($sql);
    $arr_turmas = array();
    $ano_letivo = (isset($_GET['al']) ? get_proximo_ano_letivo(get_ano_letivo()) : get_ano_letivo());
    // filtrar pelo ano letivo
    /* $ano_letivo = '2024/25'; */
    /* $ano_letivo = get_ano_letivo(); */
    foreach($res as $k => $v) {
        if($v['ano_letivo'] == $ano_letivo) {
            $arr_turmas[] = $v;
        }
    }
    return $arr_turmas;
}

function buscar_nome_turmas_participa_curso($id_user, $id_curso) {
    $sql = "SELECT * FROM rel_turma_user WHERE id_user = ($id_user) AND ativo = 1";
    $res = my_query($sql);
    $arr_turmas = array();
    foreach($res as $k => $v) {
        $sql = "SELECT nome_turma FROM turma WHERE id = " . $v['id_turma'] . " AND id_curso = $id_curso";
        $res2 = my_query($sql);
        $arr_turmas = array_merge($arr_turmas, $res2);
    }
    
    return $arr_turmas;
}

function buscar_turmas_participa_curso($id_user, $id_curso) {
    $sql = "SELECT * FROM rel_turma_user WHERE id_user = ($id_user) AND ativo = 1";
    $res = my_query($sql);
    $arr_turmas = array();
    foreach($res as $k => $v) {
        $sql = "SELECT * FROM turma WHERE id = " . $v['id_turma'] . " AND id_curso = $id_curso";
        $res2 = my_query($sql);
        $arr_turmas = array_merge($arr_turmas, $res2);
    }
    
    return $arr_turmas;
}

function gerar_items_navbar($id) {
    $turmas = array();
    $flag_diretor_curso = false;
    $curso = buscar_cursos_diretor($id); /* pego o curso, se for diretor de curso */ 
    if(isset($curso) && count($curso) > 0) {
        
        $turmas = buscar_turmas_curso($curso[0]['id']); /* pego as turmas todas do curso, se for diretor de curso */
        $flag_diretor_curso = true;
    }             
    if(!$flag_diretor_curso) {
        
        $sql = "SELECT * FROM rel_turma_user WHERE id_user = $id AND ativo = 1";
        $res = my_query($sql);
        
        foreach($res as $k => $v){
            $sql = "SELECT * FROM turma WHERE id = " . $v['id_turma'];
            $res2 = my_query($sql);
            $turmas = array_merge($turmas, $res2);
        }
        $turmas = buscar_turmas_diretor($id, $turmas);
    } else {
        $turmas = buscar_turmas_diretor($id, $turmas); /* pego as direções de turma */
        $turmas = array_merge($curso, $turmas);
        
    }

    return $turmas;
    
    

}

function gerar_tabelas($modulo, &$chave,$filtro) {
    global $arrConfig;
    $sql = "SELECT * FROM $modulo WHERE $filtro";
    $arrResultados = my_query($sql);
    $arrCampos = array();
    def_config_adm($modulo, $arrCampos);

    $chave = '';

    foreach($arrCampos as $kCampos => $vCampos) {
        if(isset($vCampos['chave'])) {
            if($vCampos['chave']) {
                $chave = $kCampos;
            }
        }
    }

    echo "<h1>$modulo</h1>";

    echo '
    <table class="table">
        <thead>
            <tr>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>';
    foreach($arrCampos as $kCampos => $vCampos) {
        if($vCampos['listagem']) {
            echo '<th>' . $vCampos['legenda'] . '</th>';
        }
    }
    echo '
                <th width="30">Editar</th>
                <th width="30">Apagar</th>     
    ';
    echo '</tr></thead>
    <tbody>
        <! -- <th>
            <label>
                <input type="checkbox" class="checkbox" />
            </label>
        </th> -->
        
    ';

    /* <!-- row 1 -->
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
                                    <img src="/tailwind-css-component-profile-2@56w.png" alt="Avatar Tailwind CSS Component" />
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">Hart Hagerty</div>
                                    <div class="text-sm opacity-50">United States</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            Zemlak, Daniel and Leannon
                            <br/>
                            <span class="badge badge-ghost badge-sm">Desktop Support Technician</span>
                        </td>
                        <td>Purple</td>
                        <th>
                            <button class="btn btn-ghost btn-xs">details</button>
                        </th>
                        </tr> */

    foreach($arrResultados as $linha) {
        echo '<tr>
                <th>
                    <label>
                    <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                

        ';
        foreach($arrCampos as $campo => $detalhes) {
            echo '<td>';

            $str_chave_aux = $linha[$chave];
            if ($detalhes['listagem']) {
                
                $largura = '';
                $largura = 'width="'.$detalhes['largura'].'"';
                if(isset($detalhes['opcoes'])) {
                    $conteudo = $detalhes['opcoes'][$linha[$campo]];
                } else {
                    $conteudo = $linha[$campo];
                    if(strlen($conteudo) > 70){ 
                        $conteudo = substr($conteudo, 0, 70) . '[...]';
                    }
                }

                if($detalhes['tipo'] == 'img' && $campo == 'imagem' ) {

                    echo "<td $largura><img style='max-width:60px;' src='" . $arrConfig['url_img'] . $linha['imagem'] . "'>";
                } else if ($detalhes['tipo'] == 'img' && $campo == 'icone') {
                    echo "<td $largura><img src='" . $arrConfig['url_img'] . $linha['icone'] . "'>";
                }
                else {
                    echo "<td $largura'>{$conteudo}</td>"; // e na hora de escrever eu escrevo o valor do campo fazendo o $linha[campo]

                }

            }

            echo '</td>';
        }
    }

    
    


}