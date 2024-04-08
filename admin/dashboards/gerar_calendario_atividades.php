<?php
include_once '../include/config.inc.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.css">
<script src="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.js"></script>
<link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/calendario.css' ?>">
<link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/styles.css' ?>">

<?php

function calcular_esforco_turma() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    $sql = "SELECT eventos.*, atividades.tempo_sugerido FROM atividades 
    INNER JOIN rel_atividades_turma ON atividades.id = rel_atividades_turma.id_atividade 
    INNER JOIN eventos ON eventos.id = atividades.id_evento
    WHERE rel_atividades_turma.id_turma = $id_turma";
        
    $res = my_query($sql);
    

    $sql = "SELECT esforco.* FROM esforco 
    INNER JOIN turma ON turma.id_esforco = esforco.id 
    WHERE turma.id = $id_turma";
    $esforco_turma = my_query($sql);
    $esforco_turma = array_shift($esforco_turma);
    
    $eventos_esforco = array();

    $turma_atividade_esforco = [];
    $esforco = -1;

    //calcular tempo médio diario da atividade        
    for($i = 0; $i < count($res); $i++) {
        $atividade = $res[$i];
        $tempo_medio_diario = 0;
        $comeco = new DateTime($atividade['comeco']);
        $fim = new DateTime($atividade['fim']);
        if($comeco == $fim) {
            $tempo_medio_diario = $atividade['tempo_sugerido'];
        } else {
            $dias = $comeco->diff($fim)->days;
            $tempo_medio_diario = $atividade['tempo_sugerido'] / $dias;
        }
        $res[$i]['tempo_medio_diario'] = $tempo_medio_diario;
        
    }
    // fim do calculo, agora cada atividade tem o tempo médio diario

    // pegar o dia da primeira atividade e o dia da última atividade
    $menor_data = new DateTime($res[0]['comeco']);
    $maior_data = new DateTime($res[0]['fim']);

    for($i = 0; $i < count($res); $i++) {
        $comeco = new DateTime($res[$i]['comeco']);
        $fim = new DateTime($res[$i]['fim']);
        if($comeco < $menor_data) {
            $menor_data = $comeco;
        }
        if($fim > $maior_data) {
            $maior_data = $fim;
        }
    }

    // percorrer todas as datas entre o intervalo, e verificar o esforço em cada dia
    $intervalo = new DateInterval('P1D'); 
    $maior_data->modify('+1 day');
    $daterange = new DatePeriod($menor_data, $intervalo, $maior_data);
    $esforco = 0;
    foreach($daterange as $data) {
        $data = $data->format('Y-m-d');
        $esforco_dia = 0;
        for($i = 0; $i < count($res); $i++) {
            $comeco = new DateTime($res[$i]['comeco']);
            $fim = new DateTime($res[$i]['fim']);
            if($data >= $comeco->format('Y-m-d') && $data <= $fim->format('Y-m-d')) {
                $esforco_dia += $res[$i]['tempo_medio_diario'];
            }
        }
        $esforco += $esforco_dia;
        $eventos_esforco[$data] = $esforco_dia;
    }
    /* pr($eventos_esforco); */

    $arr_eventos = array();

    foreach($eventos_esforco as $k => $evento) {
        if($evento > $esforco_turma['limite']) {
            $arr_eventos[] = array(
                'title' => '>= ' . $esforco_turma['limite'] . ' horas',
                'start' => $k,
                'end' => $k,
                'backgroundColor' => '#1E3A8A'
            );
        } else if($evento > $esforco_turma['barreira'] && $evento < $esforco_turma['limite']) {
            $arr_eventos[] = array(
                'title' => '>= ' . $esforco_turma['barreira'] . ' horas',
                'start' => $k,
                'end' => $k,
                'backgroundColor' => '#3B82F6'
            );
        } else if($evento < $esforco_turma['barreira'] && $evento > 0) {
            $arr_eventos[] = array(
                'title' => '< ' . $esforco_turma['barreira'] . ' horas',
                'start' => $k,
                'end' => $k,
                'backgroundColor' => '#BFDBFF'
            );
        } else if($evento == 0) {
            
        }
    }

    /* pr($arr_eventos); */

    return $arr_eventos;

    
        
    
}
function gerar_calendario() {
    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    $eventos = calcular_esforco_turma();    
    

    $html = ' 
    
    <div id="ec"></div>
    

    <script>
        let eventos = ' . json_encode($eventos) . ';
        console.log(eventos);
        let ec = new EventCalendar(document.getElementById(\'ec\'), {
            view: \'dayGridMonth\',
            allDaySlot: false,         
            
            events: eventos,
        });

    </script>
    
    ';
    return $html;
}
?>
<!-- <script>

let ec = new EventCalendar(document.getElementById('ec'), {
    view: 'dayGridMonth',
    allDaySlot: false,         
      
    events: [
        // your list of events
    ]
});

</script> -->


