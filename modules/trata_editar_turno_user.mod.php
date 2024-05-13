<?php include '../include/config.inc.php';

pr($_POST);
$cargo = $_SESSION['cargo'];
$id_turma = $_POST['id_turma'];
if($cargo == 'alunos') {
    foreach($_POST as $key => $value){
        $key = str_replace('turno_', '', $key);
        $id_user = $key;
    }
    $id_turno = $_POST['turno_'.$key];
    $sql = "SELECT id FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_turma";
    $id_rel_turma_user = my_query($sql);
    $sql = "UPDATE rel_turno_user SET id_turno = $id_turno WHERE id_rel_turma_user = {$id_rel_turma_user[0]['id']}";
    my_query($sql);
} else {
    $novo_array = array();

    // Itera sobre o array original
    foreach ($_POST as $chave => $valor) {
        // Verifica se é um id de usuário
        if (strpos($chave, 'id_user$') !== false) {
            $id_usuario = $valor;
            // Inicializa um novo array para o usuário se não existir
            if (!isset($novo_array[$id_usuario])) {
                $novo_array[$id_usuario] = array();
            }
        } elseif (strpos($chave, 'turno_') !== false) {
            // Se for um turno, adiciona ao array do usuário correspondente
            $partes = explode('%', $chave);
            $id_turno = $partes[1];
            $novo_array[$id_usuario][] = $id_turno;
        }
    }
    pr($novo_array);
    foreach($novo_array as $id_user => $turnos) {
        $sql = "SELECT id FROM rel_turma_user WHERE id_user = $id_user AND id_turma = $id_turma";
        $id_rel_turma_user = my_query($sql);
        $sql = "DELETE FROM rel_turno_user WHERE id_rel_turma_user = {$id_rel_turma_user[0]['id']}";
        my_query($sql);
        foreach($turnos as $id_turno) {
            $sql = "INSERT INTO rel_turno_user (id_rel_turma_user, id_turno) VALUES ({$id_rel_turma_user[0]['id']}, $id_turno)";
            my_query($sql);
        }
    
    }
}
header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma . '&tab=' . $cargo . '');


