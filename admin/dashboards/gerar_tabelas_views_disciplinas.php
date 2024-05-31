<?php

include '../../include/config.inc.php';
$valor = $_GET['valor'];
$id_instituicao = $_SESSION['id_instituicao'];
$sql = "SELECT rel_disciplina_curso.id_disciplina, disciplinas.*
    FROM rel_disciplina_curso 
    INNER JOIN disciplinas ON rel_disciplina_curso.id_disciplina = disciplinas.id 
    INNER JOIN rel_instituicao_disciplinas ON id_disc = disciplinas.id
    WHERE rel_disciplina_curso.id_curso = " . $_SESSION['id_curso'] . " AND rel_instituicao_disciplinas.id_instituicao = $id_instituicao";
$res = my_query($sql);


switch ($valor) {
    
    case 'disciplinas': {
        
        $sql = "SELECT id, duracao FROM curso WHERE id_diretor_curso = " . $_SESSION['id'];
        $res2 = my_query($sql);
        $duracao = $res2[0]['duracao'];
        $id_curso = $res2[0]['id'];
        $_SESSION['id_curso'] = $id_curso;
        


        $html = '
                <!-- head -->
                <thead>
                    <tr>                        
                        <th>ID</th>
                        <th>Nome</th>                        
                        
                        '; 
        
        for($i = 1; $i <= $duracao; $i++) {
            $html .= '<th>' . $i + 9 . 'º Ano</th>';
        }
                        
        $html .= '
                        <th>Ativo</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
        
                ';

                    
foreach ($res as $disciplina) {
    $editar = (isset($_GET['editar']) ? $_GET['editar'] : false);
    
    $html .= '
                    <tr>                        
                        <td>' . $disciplina['id'] . '</td>
                        <td>
                        ' . $disciplina['nome'] . '
                        <br>
                        <span class="badge badge-ghost badge-sm">' . $disciplina['abreviatura'] . '</span>
                        </td>
                        
                        '; 
                        
                        for($i = 1; $i <= $duracao; $i++) {
                            $sql = "SELECT * FROM rel_disciplina_curso_ano WHERE id_disciplina = " . $disciplina['id'] . " AND id_curso = $id_curso AND ano = $i";
                            $res_rel = my_query($sql);
                            
                            
                            if(count($res_rel) > 0) {
                                if($res_rel[0]['ano'] == $i) {
                                    $html .= '<td><input type="checkbox" name="' . $disciplina['id'] . '%' . $i . '" class="checkbox" ' . ($editar ? '' : 'disabled') . ' checked /></td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                            } else {
                                $html .= '<td><input type="checkbox" name="' . $disciplina['id'] . '%' . $i . '" class="checkbox" ' . ($editar ? '' : 'disabled') . '  /></td>';
                            }
                        }

                        $html .= '
                        <td>' . ($disciplina['ativo'] ? 'Sim' : 'Não') . '</td>
                        <td>                            
                        <a onClick="
                                
                        Swal.fire({
                            title: \'Tem certeza que deseja remover a disciplina?\',
                            text: \'Ao remover essa disciplina, todos os users que estiverem relacionados a ela, perderão essa relação, deseja prosseguir?\',
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
                    <a ' . ($editar ? 'onclick="document.getElementById(\'form_disciplinas\').submit();"' : 'href="' . $arrConfig['url_admin'] . 'curso.php?editar=true&tab=disciplinas' . '"') . '  class="btn btn-ghost btn-xs">
                        ' . ($editar ? 'Confirmar' : '<i class="fas fa-edit"></i>') . '
                    </a>
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

