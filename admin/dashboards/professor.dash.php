<?php
include '../include/config.inc.php';
include_once 'layout.dash.php';

$flag_direcao_turma = false;
$turmas = gerar_items_navbar($_SESSION['id']);
navbar($turmas); 

if(isset($_GET['id_turma']) && $_GET['id_turma'] != '') {
    $flag_turma_selecionada = true;
    $id_turma = $_GET['id_turma'];
    $sql = "SELECT * FROM turma WHERE id = $id_turma";
    $res = my_query($sql);
} else {
    $flag_turma_selecionada = false;
}




echo $flag_turma_selecionada ? '<div class="flex justify-center items-center">' . turma($res, $flag_direcao_turma) . '</div>' : '<div class="flex justify-center items-center"><h1 class="mt-10">nenhuma turma selecionada</h1></div>';

