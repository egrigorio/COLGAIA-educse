<?php
include '../../include/config.inc.php';
$valor = $_GET['valor'];

$id_user_diretor_curso = $_SESSION['id'];
$sql = "SELECT * FROM view_user_curso 
        WHERE cargo = 'professor' AND id_diretor_curso = $id_user_diretor_curso";

$res = my_query($sql);


switch ($valor) {
    
    case 'efetivos': {                                
        $sql = "SELECT id FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
        $res2 = my_query($sql);
        $id_curso = $res2[0]['id'];
        $_SESSION['id_curso'] = $id_curso;        
        $html = '
        <!-- head -->
                <thead>
                    <tr>
                        <th>
                            
                        </th>
                        <th>Nome</th>
                        <th>Disciplinas</th>
                        <th>Turmas</th>
                        <th>Direção de Turma</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
        
                ';

foreach ($res as $professor) {
if ($professor['estado'] == '1') {

    $direcao_turma = '';
    $direcao_turma = buscar_direcao_turma($professor['id_user']);
    $disciplinas = buscar_disciplinas_cargo($professor['id_user'], 'professor', $id_curso);
    $al = isset($_GET['al']) ? $_GET['al'] : 0;
    if($al) {
        $ano_letivo = get_proximo_ano_letivo(get_ano_letivo());
    } else {
        $ano_letivo = get_ano_letivo();
    }
    $arr_nome_turmas_participa = buscar_nome_turmas_participa_curso($professor['id_user'], $id_curso, $ano_letivo);
    count($direcao_turma) == 0 ? $direcao_turma = 'Nenhuma turma' : $direcao_turma = $direcao_turma[0]['nome_turma'];
    $nome_turmas_participa = '';
    foreach ($arr_nome_turmas_participa as $turma) {
        $nome_turmas_participa .= $turma['nome_turma'] . ', ';
    }
    $nome_turmas_participa != '' ? $nome_turmas_participa = substr($nome_turmas_participa, 0, -2) : $nome_turmas_participa = 'Nenhuma turma';
    $html .= '
                    
                    <tr>
                        <th>
                            
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
    foreach ($disciplinas as $disciplina) {

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
                                <a href="' . $arrConfig['url_admin'] . 'editar/professores_curso.php?id_user=' . $professor['id_user'] . '" class="btn btn-ghost btn-xs">
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $professor['id_user'] . '\';
                                            
                                        }
                                    });

                                " class="btn btn-ghost btn-xs">
                                    <i class="fas fa-trash"></i>
                                </a>
                                
                                    
                                
                            </th>
                        </tr>

                        ';
}
}

$html .= '

                    
                </tbody>                    

                <!-- body -->
                <!-- foot -->
                
                <!-- foot -->
                ';
        echo $html;
    } 

    break;
    case 'convidados': {
        $sql = "SELECT *
        FROM conf_convite 
        WHERE id_curso = " . $_SESSION['id_curso'] . " AND cargo = 'professor'" . 
        " AND email NOT IN (
            SELECT email
            FROM users
        )";
        $res_emails_convidados_nao_registados = my_query($sql);
        $html = '
                <!-- head -->
                <thead>
                    <tr>
                        <th>
                            <label>
                            <input type="checkbox" class="checkbox" />
                            </label>
                        </th>
                        <th>Nome</th>
                        <th>Estado</th>
                        <th>Opções</th>
                        
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
        
                ';
        foreach ($res as $professor) {
            

            if($professor['estado'] !== '1') {
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
                            <td>' . $professor['estado'] . '</td>
                                
                            
                            <th>
                            
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $professor['id_user'] . '\';
                                            
                                        }
                                    });
                                
                                " class="btn btn-ghost btn-xs">
                                    <i class="fas fa-trash"></i>
                                </a>                                
                            </th>
                                
                                
                        </tr>';

            }

            
        }
        foreach($res_emails_convidados_nao_registados as $email) {
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
                                        <img src="' . $arrConfig['url_pfp'] . 'e.png' . '" alt="PFP do ' . $email['email'] . '" />
                                        </div>
                                    </div>
                                    <div>                                        
                                        <div class="text-sm opacity-50">' . $email['email'] . '</div>
                                    </div>
                                </div>
                            </td>                                                                            
                            <td>Email não inscrito na plataforma, convite enviado.</td>
                                                                                        
                            <th>
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_convite_user_curso.mod.php?id_user=' . $email['id'] . '\';
                                            
                                        }
                                    });

                                " class="btn btn-ghost btn-xs">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </th>
                                
                                
                        </tr>';
        }
        echo $html;
        };

    
    break;
}

