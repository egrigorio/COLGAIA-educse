<?php
include '../../include/config.inc.php';
$valor = $_GET['valor'];
$id_user_diretor_curso = $_SESSION['id'];
$sql = "SELECT * FROM view_user_curso 
        WHERE cargo = 'aluno' AND id_diretor_curso = $id_user_diretor_curso";

$res = my_query($sql);
/* pr($res);
die; */
switch ($valor) {
    
    case 'efetivos': {
        
        $sql = "SELECT id FROM curso WHERE id_diretor_curso = $id_user_diretor_curso";
        $res2 = my_query($sql);

        $id_curso = $res2[0]['id'];
        $_SESSION['id_curso'] = $id_curso;
        


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
                        <th>Turma</th>                        
                        <th>Opções</th>
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
        
                ';

        
foreach ($res as $aluno) {
    if ($aluno['estado'] == '1') {
        /* pr($aluno); */ 
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
                                        <img src="' . $arrConfig['url_pfp'] . $aluno['pfp'] . '" alt="PFP do ' . $aluno['username'] . '" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">' . $aluno['username'] . '</div>
                                        <div class="text-sm opacity-50">' . $aluno['email'] . '</div>
                                    </div>
                                </div>
                            </td>                                                                                                                                                                                    
                            ';
        $sql = "SELECT turma.nome_turma 
        FROM rel_turma_user 
        INNER JOIN turma ON rel_turma_user.id_turma = turma.id
        WHERE rel_turma_user.id_user = " . $aluno['id_user'] . " AND rel_turma_user.ativo = 1";
        $res_turma = my_query($sql);
        $res_turma ? $nome_turma = $res_turma[0]['nome_turma'] : $nome_turma = 'Sem turma';
        $al = isset($_GET['al']) ? $_GET['al'] : '';
        if($al) {
            $ano_letivo = get_proximo_ano_letivo(get_ano_letivo());
        } else {
            $ano_letivo = get_ano_letivo();
        }
        $sql = "SELECT nome_turma, id FROM turma WHERE id_curso = $id_curso AND ano_letivo = '$ano_letivo'";
        $res_turmas = my_query($sql);        
        $sql = "SELECT rel_turma_user.id_turma FROM rel_turma_user INNER JOIN turma ON rel_turma_user.id_turma = turma.id WHERE rel_turma_user.id_user = " . $aluno['id_user'] . " AND rel_turma_user.ativo = 1 AND turma.ano_letivo = '$ano_letivo'";
        
        
        $res_turma_user_participa = my_query($sql);        
        $res_turma_user_participa = array_shift($res_turma_user_participa);        
        $options = '';
        foreach($res_turmas as $k => $v) {
            $options .= '<option ' . ($res_turma_user_participa ? ($res_turma_user_participa['id_turma'] == $v['id'] ? 'selected' : '') : '') . ' value="' . $v['id'] . '">' . $v['nome_turma'] . '</option>';                        
        }
        if($options == '') {
            $options = '<option value="Sem turmas">Sem turmas</option>';
        }
        
         
        $html .= '
                            <td>' 
                            . 
                            
                                (isset($_GET['editar']) ? 
                                '
                                <select name="select_id_turma%' . $aluno['id_user'] . '" class="select w-full max-w-xs">
                                    ' . 
                                    $options
                                    . '                                                                                                                                                 
                                </select>
                                '
                                :
                                '
                                <input type="text" class="input w-full max-w-xs" value="' . $nome_turma . '" disabled></input>
                                ')

                            .
                             '</td>
                            
                            <th>
                                ' . (isset($_GET['editar']) ? 
                                '
                                <a onclick="document.getElementById(\'form_edicao_alunos_curso\').submit();" class="btn btn-ghost btn-xs">
                                    Confirmar
                                </a>
                                ' 
                                : 
                                '
                                <a href="' . $arrConfig['url_admin'] . 'curso.php?editar=true&tab=alunos" class="btn btn-ghost btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                '
                                ) . '
                                
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $aluno['id_user'] . '\';
                                            
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
                <tfoot>
                    <tr>
                        <th> </th>
                        <th>Nome</th>
                        <th>Turma</th>
                        <th>Opções</th>
                    </tr>
                </tfoot>
                <!-- foot -->
                ';
        echo $html;
    } 

    break;
    case 'convidados': {
        $sql = "SELECT *
        FROM conf_convite 
        WHERE id_curso = " . $_SESSION['id_curso'] . " AND cargo = 'aluno'" .
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

