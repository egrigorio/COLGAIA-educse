<?php
include '../include/config.inc.php';
pr($_POST);
$id_turma = $_GET['id_turma'];
$dias = ['dia_0', 'dia_1', 'dia_2', 'dia_3', 'dia_4', 'dia_5', 'dia_6'];

$valores_com_on = $_POST;
foreach($dias as $dia) {
    if(isset($valores_com_on[$dia])) {
        $valores_com_on[$dia] = 1;
    } else {
        $valores_com_on[$dia] = 0;
    }
}
$id_esforco = $_POST['id_esforco'];
$limite = $_POST['limite'];
$barreira = $_POST['barreira'];
$dia_0 = $valores_com_on['dia_0'];
$dia_1 = $valores_com_on['dia_1'];
$dia_2 = $valores_com_on['dia_2'];
$dia_3 = $valores_com_on['dia_3'];
$dia_4 = $valores_com_on['dia_4'];
$dia_5 = $valores_com_on['dia_5'];
$dia_6 = $valores_com_on['dia_6'];

$sql = "UPDATE esforco 
SET limite = $limite, barreira = $barreira, dia_0 = $dia_0, dia_1 = $dia_1, dia_2 = $dia_2, dia_3 = $dia_3, dia_4 = $dia_4, dia_5 = $dia_5, dia_6 = $dia_6 
WHERE id = $id_esforco";
my_query($sql);

header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);