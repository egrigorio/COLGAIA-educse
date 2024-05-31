<?php include '../../include/config.inc.php';

$ano_letivo = $_GET['ano_letivo'];
$sql = "SELECT * FROM turma WHERE id_curso = " . $_SESSION['id_curso'] . " AND ano_letivo = '$ano_letivo'";
$res = my_query($sql);
$ano_letivo_atual = get_ano_letivo();
$flag_ano_letivo = false;

if($ano_letivo == $ano_letivo_atual) {
    $flag_ano_letivo = true;
}    


$html = '        
    <form method="post" action="' . $arrConfig['url_modules'] . 'trata_editar_turma_diretor_curso.mod.php' . '">
    <div class="overflow-x-auto">
        <table class="table">
            <!-- head -->
            <thead>
            <tr>
                <th>ID</th>            
                <th>Nome da Turma</th>
                <th>Ano Letivo</th>
                <th>Diretor de Turma</th>
                <th>Turnos</th>
                '; if($flag_ano_letivo) $html .= '<th>Editar</th>'; 
                $html .= '
                <th>Ver +</th>

            </tr>
            </thead>
            <tbody>
            '; 
            
            foreach($res as $turma) {     
                $id_turma = $turma['id'];           
                $sql = "SELECT turno.* FROM view_turno_turma 
                INNER JOIN turno ON turno.id = view_turno_turma.id_turno 
                WHERE id_turma = $id_turma ORDER BY numero ASC";
                $res_turno = my_query($sql);
                $sql = "SELECT username FROM users WHERE id = " . $turma['id_diretor_turma'];
                $res_dt = my_query($sql);
                $res_dt = array_shift($res_dt);
                $editar = isset($_GET['editar']) ? $_GET['editar'] : '';
                $sql = "SELECT * FROM rel_turma_user 
                INNER JOIN users ON users.id = rel_turma_user.id_user
                WHERE rel_turma_user.id_turma = " . $turma['id'] . " AND rel_turma_user.ativo = 1 AND users.cargo = 'professor' AND users.id NOT IN (
                    SELECT id_diretor_turma FROM turma
                )";
                
                $res_professores = my_query($sql);
                // remover o diretor de turma da lista de professores
                foreach($res_professores as $k => $professor) {
                    if($professor['id'] == $turma['id_diretor_turma']) {
                        unset($res_professores[$k]);
                    }
                }
                $user_dt = $res_dt ? $res_dt['username'] : '';
                $html .= '        
                
                <tr class="hover">            
                    <td>' . $turma['id'] . '</td>
                    <td>' . $turma['nome_turma'] . '</td>
                    <td>' . $turma['ano_letivo'] . '</td>
                    ';
                    if(isset($_GET['editar'])) {                                                
                        $html .= '
                        <td>
                            
                                <select name="diretor_turma_' . $turma['id'] . '" class="select select-bordered">
                                    
                                    <option value="' . $turma['id_diretor_turma'] . '">' . ($user_dt ? $user_dt : 'Sem diretor de turma') . '</option>
                                ';
                        
                                foreach($res_professores as $professor) {
                                    $html .= '<option value="' . $professor['id'] . '">' . $professor['username'] . '</option>';
                                }
                                $html .= '
                                    ' . ($user_dt ? '<option value="-1">Sem diretor de turma</option>' : '') . '
                                </select>
                            
                        </td>
                            ';
                                
                    } else {
                        $html .= '
                        <td>
                            <label class="form-control w-full max-w-xs">                            
                                <input type="text" value="' . ($res_dt ? $res_dt['username'] : 'Sem Diretor de Turma') . '" class="input w-full max-w-xs" disabled />
                            </label>
                        </td>
                        ';
                    }
                    $html .= '<td>';
                    $numeros = []; // array para evitar repetição de turnos

                    foreach($res_turno as $turno) {                        
                        if(in_array($turno['numero'], $numeros) || $turno['numero'] == 0) {
                            continue;
                        } else {
                            $html .= 'Turno ' . $turno['numero'] . '<br>';
                            $numeros[] = $turno['numero'];
                        }
                    }
                    $html .= '</td>';
                    if($flag_ano_letivo) {
                        if(isset($_GET['editar'])) {
                            $html .= '
                            <td>
                                <input type="submit" class="btn btn-ghost btn-xs" value="Confirmar">            
                            </td>
                            ';
                        } else {

                            $html .= '
                        
                                    <td>
                                        <a href="?editar=true" class="btn btn-ghost btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                        
                        ';                        
                        }
                    }

                    $html .= '
                    
                    <td>
                        <a href="' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $turma['id'] . '" class="btn btn-ghost btn-xs">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>                                
                ';
            }

            $html .= '            
            </tbody>
        </table>
    </div>
    </form>    
    ';


echo $html;

