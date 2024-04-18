<?php 
include '../../include/config.inc.php'; 

$id_turma = $_GET['id_turma'];
$sql = "SELECT users.*, turno.numero AS turno 
FROM rel_turno_user 
INNER JOIN users ON users.id = rel_turno_user.id_user 
INNER JOIN turno ON turno.id = rel_turno_user.id_turno 
WHERE rel_turno_user.id_turma = $id_turma AND users.cargo = 'aluno' " . ((isset($_GET['turno_numero']) && $_GET['turno_numero'] != 'all') ? "AND turno.numero = " . $_GET['turno_numero'] : "");


$res = my_query($sql);



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
                        <th>Turno</th>
                        <th>Opções</th>

                    </tr>
                </thead>
                <!-- head -->
                <!-- body -->

                <tbody>          
                
                ';
foreach ($res as $aluno) {
    
    
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
        WHERE rel_turma_user.id_user = " . $aluno['id'] . " AND rel_turma_user.ativo = 1";
        $res_turma = my_query($sql);
        
        
        $res_turma ? $nome_turma = $res_turma[0]['nome_turma'] : $nome_turma = 'Sem turma';
        /* $_GET['editar'] = 'true'; */
        
        
        $html .= '
                            <td>' . $nome_turma . '</td> 
                            
                            
                            ';                            
                            if(isset($_GET['editar']) && $_GET['editar'] == 'true') {   
                                
                                                             
                                $html .= '                                                                               
                                <input name="id_turma" value=' . $_GET['id_turma'] . ' type="hidden" class="input w-14 max-w-xs">
                                <td>
                                    <select name="turno_'. $aluno['id'] .'" class="input w-14 max-w-xs">
                                        
                                    ';

                                $sql = "SELECT numero FROM turno INNER JOIN rel_turno_user ON turno.id = rel_turno_user.id_turno WHERE rel_turno_user.id_turma = " . $id_turma;
                                $res_turno = my_query($sql);
                                $numeros = array();
                                foreach($res_turno as $turno) {
                                    if(!in_array($turno['numero'], $numeros)) {                                      
                                        $numeros[] = $turno['numero'];
                                        $html .='
                                        <option ' . ($turno['numero'] == $aluno['turno'] ? 'selected' : '') . ' value="' . $turno['numero'] . '">' . $turno['numero'] . '</option>
                                        ';
                                    } else {
                                        continue;
                                    }
                                }
                                $html .= '
                                    </select>
                                    <!-- <label class="form-control w-full max-w-xs">                        
                                        <input type="number" placeholder="0" name="turno_'. $aluno['id'] .'" value="' . $aluno['turno'] . '" class="input w-14 max-w-xs" />
                                    </label> -->
                                </td>                          
                                ';
                            } else {                                
                                $html .= '                    
                                <td>
                                    <label class="form-control w-full max-w-xs">                        
                                        <input type="number" placeholder="0" name="turno_'. $aluno['id'] .'" value="' . $aluno['turno'] . '" class="input w-14 max-w-xs" disabled />
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
    