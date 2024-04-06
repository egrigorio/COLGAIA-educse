<?php
include '../../include/config.inc.php';
$valor = $_GET['valor'];

$sql = "SELECT users.*, estado FROM users
        JOIN rel_user_curso ON users.id = rel_user_curso.id_user
        JOIN curso ON rel_user_curso.id_curso = curso.id
        WHERE curso.id_diretor_curso = " . $_SESSION['id'] . " AND rel_user_curso.cargo = 'aluno'";
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
        WHERE rel_turma_user.id_user = " . $aluno['id'];
        $res_turma = my_query($sql);
        $res_turma ? $nome_turma = $res_turma[0]['nome_turma'] : $nome_turma = 'Sem turma';
        
        
         
        $html .= '
                            <td>' . $nome_turma . '</td>
                            
                            <th>
                                <a href="' . $arrConfig['url_admin'] . 'editar/alunos_curso.php?id_user=' . $aluno['id'] . '" class="btn btn-ghost btn-xs">
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $aluno['id'] . '\';
                                            
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
                                            window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_user_curso.mod.php?id_user=' . $professor['id'] . '\';
                                            
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

