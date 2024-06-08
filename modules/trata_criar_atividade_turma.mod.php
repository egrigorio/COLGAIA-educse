<?php
include '../include/config.inc.php';


$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$comeco = $_POST['comeco'];
$fim = $_POST['fim'];
$tipo = $_POST['tipo'];
$disciplina = $_POST['disciplina'];
$turno = $_POST['turno'] ? $_POST['turno'] : 'NULL';
$turno == 'all' ? $turno = -1 : $turno = $turno;
$tempo_sugerido = $_POST['tempo_sugerido'];
$id_turma = $_POST['id_turma'];
$id_professor = $_SESSION['id'];

$sql = "SELECT * FROM esforco 
        INNER JOIN turma ON turma.id_esforco = esforco.id 
        WHERE turma.id = $id_turma";
$res_esforco = my_query($sql);

$begin = new DateTime($_POST['comeco']);
$end = new DateTime($_POST['fim']);
$end = $end->modify('+1 day'); // Adiciona um dia ao fim para incluir essa data no intervalo

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval ,$end);

$tabela_correspondencias_dias_semana = [
    'Monday' => 'dia_0',
    'Tuesday' => 'dia_1',
    'Wednesday' => 'dia_2',
    'Thursday' => 'dia_3',
    'Friday' => 'dia_4',
    'Saturday' => 'dia_5',
    'Sunday' => 'dia_6'    
];

$flag_atividades = false;

foreach($daterange as $date){
    echo $date->format("Y-m-d") . ": " . $date->format("l") . "<br>";
    $dia = $tabela_correspondencias_dias_semana[$date->format("l")];
    if($res_esforco[0][$dia] == 1) {
        $flag_atividades = true;
    }

}

if($flag_atividades) {
    if($comeco > $fim){
        $_SESSION['erro'] = "Data de início não pode ser maior que a data de fim";
        header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
        exit;
    } else if($comeco == $fim) {
        $_SESSION['erro'] = "Data de início não pode ser igual a data de fim";
        header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
        exit;
    } else if($comeco < date('Y-m-d')) {
        $_SESSION['erro'] = "Data de início não pode ser menor que a data atual";
        header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
        exit;
    } else if($fim < date('Y-m-d')) {
        $_SESSION['erro'] = "Data de fim não pode ser menor que a data atual";
        header('Location: ' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $id_turma);
        exit;
    }
    $sql = "INSERT INTO eventos (titulo, comeco, fim, ativo) VALUES ('$titulo', '$comeco', '$fim', 1)";
    $id_evento = my_query($sql);
    
    $sql = "INSERT INTO atividades (id_evento, descricao, tipo, id_professor, tempo_sugerido, id_disciplina, id_turno, ativo) VALUES ($id_evento, '$descricao', '$tipo', '$id_professor' , $tempo_sugerido, $disciplina, $turno, 1)";
    $id_atividade = my_query($sql);
    
    $sql = "INSERT INTO rel_atividades_turma (id_atividade, id_turma) VALUES ($id_atividade, $id_turma)";
    my_query($sql);
} else {
    $_SESSION['erro'] = "Não foi possível efetuar a criação das atividades, uma vez que as datas incluidas englobam, apenas, dias de descanso.";
}


header('Location: ' . $arrConfig['url_admin'] . 'turma.php?tab=atividade&id_turma=' . $id_turma );