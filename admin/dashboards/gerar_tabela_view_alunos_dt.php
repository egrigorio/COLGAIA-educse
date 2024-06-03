<?php 
include '../../include/config.inc.php'; 

$id_turma = $_GET['id_turma'];

/* $sql = "SELECT * FROM view_aluno_turno_turma WHERE id_turma = $id_turma " . ((isset($_GET['turno_numero']) && $_GET['turno_numero'] != 'all') ? "AND num_turno = " . $_GET['turno_numero'] : "");
$res = my_query($sql); */
$sql = "SELECT * FROM view_user_curso 
            INNER JOIN rel_turma_user ON rel_turma_user.id_user = view_user_curso.id_user   
            INNER JOIN rel_turno_user ON rel_turno_user.id_rel_turma_user = rel_turma_user.id           
            WHERE cargo = 'aluno' AND id_turma = $id_turma";
$res = my_query($sql);
/* echo $sql;
pr($res);
die; */



$html = '
                <!-- head -->
                <input type="hidden" name="cargo" value="alunos">
                <thead>
                    <tr>
                        
                        <th>Nome</th>
                        <th>Turma</th>
                        <th>Turno</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>
                ';
foreach ($res as $aluno) {
    /* pr($res); */
    if($aluno['id_turno'] != -1) {
        $sql = "SELECT * FROM turno WHERE id = " . $aluno['id_turno'];
        $res_turno = my_query($sql);
        $aluno['num_turno'] = $res_turno[0]['numero'];
    } else {
        $aluno['num_turno'] = 0;
    }

    $html .= '
                         <tr>
                            
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
        $html .= '
                            <td>' . $nome_turma . '</td>
                            '; 
                            if(isset($_GET['editar']) && $_GET['editar'] == 'true') {   
                                
                                                             
                                $html .= '                                                                               
                                <input name="id_turma" value=' . $_GET['id_turma'] . ' type="hidden" class="input w-14 max-w-xs">
                                <td>
                                    <select name="turno_'. $aluno['id_user'] .'" class="input w-14 max-w-xs">
                                        
                                    ';

                                
                                $sql = "SELECT * FROM view_turno_turma WHERE id_turma = $id_turma AND numero <> 0";
                                $res_turno = my_query($sql);
                                /* pr($res_turno);
                                die; */
                                $numeros = array();                                                                
                                foreach($res_turno as $turno) {                                    
                                    if(!in_array($turno['numero'], $numeros)) {                                      
                                        $numeros[] = $turno['numero'];
                                        $html .='
                                        <option ' . ($turno['numero'] == $aluno['num_turno'] ? 'selected' : '') . ' value="' . $turno['id_turno'] . '">' . $turno['numero'] . '</option>
                                        ';
                                    } else {
                                        continue;
                                    }
                                }
                                $html .= '
                                    </select>                                    
                                </td>                          
                                ';
                            } else {                                
                                $html .= '                    
                                <td>
                                    <label class="form-control w-full max-w-xs">                        
                                        <input type="number" placeholder="0" name="turno_'. $aluno['id_turno'] .'" value="' . $aluno['num_turno'] . '" class="input w-14 max-w-xs" disabled />
                                    </label>
                                </td>
                                ';
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
                                            <a href="?id_turma=' . $_GET['id_turma'] . '&editar=true&tab=alunos " class="btn btn-ghost btn-xs">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                            
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
    