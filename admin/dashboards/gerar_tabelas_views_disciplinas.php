<?php

include '../../include/config.inc.php';
$valor = $_GET['valor'];
$id_instituicao = $_SESSION['id_instituicao'];
$sql = "SELECT rel_disciplina_curso.id_disciplina, disciplinas.*
    FROM rel_disciplina_curso 
    INNER JOIN disciplinas 
    WHERE rel_disciplina_curso.id_disciplina = disciplinas.id";
$res = my_query($sql);


switch ($valor) {
    
    case 'disciplinas': {
        
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
                        <th>ID</th>
                        <th>Nome</th>                        
                        <th>Duração (em anos)</th>
                        <th>Ativo</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
        
                ';

        
foreach ($res as $disciplina) {
    $html .= '
                    <tr>
                        <td>
                            <label>
                            <input type="checkbox" class="checkbox" />
                            </label>
                        </td>
                        <td>' . $disciplina['id'] . '</td>
                        <td>
                        ' . $disciplina['nome'] . '
                        <br>
                        <span class="badge badge-ghost badge-sm">' . $disciplina['abreviatura'] . '</span>
                        </td>
                        <td>' . $disciplina['duracao'] . '</td>
                        <td>' . $disciplina['ativo'] . '</td>
                        <td>                            
                        <a onClick="
                                
                        Swal.fire({
                            title: \'Tem certeza que deseja remover a disciplina?\',
                            text: \'Essa ação não pode ser revertida!\',
                            icon: \'warning\',
                            showCancelButton: true,
                            confirmButtonColor: \'#3085d6\',
                            cancelButtonColor: \'#d33\',
                            cancelButtonText: \'Cancelar\',
                            confirmButtonText: \'Sim, remover!\'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = \'' . $arrConfig['url_modules'] . 'trata_excluir_disciplina_curso.mod.php?id_disc=' . $disciplina['id'] . '\';
                                
                                
                            }
                        });

                    " class="btn btn-ghost btn-xs"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    ';
}

$html .= '

                    
                </tbody>                    

                <!-- body -->
                <!-- foot -->
                <tfoot>
                <tr>
                
                <th> </th>
                <th>ID</th>
                <th>Nome</th>                
                <th>Duração (em anos)</th>
                <th>Ativo</th>
                <th>Opções</th>
            </tr>
                </tfoot>
                <!-- foot -->
                ';
        echo $html;
    } 

    break;
    
}

