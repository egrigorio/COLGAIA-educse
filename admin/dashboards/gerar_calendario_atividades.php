
<?php
function calcular_esforco_dia($data, $data_certa, $res, $esforco_turma) {
        
    // o que é a data? é a data do dia que eu quero calcular o esforço
    // o que é o data_certa? é a data do dia que eu quero calcular o esforço, mas em formato de DateTime
    // o que é o res? é o resultado da query que eu fiz para pegar as atividades da turma
    // o que é o esforco_turma? é o resultado da query que eu fiz para pegar o esforço da turma    
    // tempo_medio_diario é o tempo médio diário da atividade
    $esforco_dia = 0;
    for($i = 0; $i < count($res); $i++) {
        $comeco = new DateTime($res[$i]['comeco']);
        $fim = new DateTime($res[$i]['fim']);
        if($data >= $comeco->format('Y-m-d') && $data <= $fim->format('Y-m-d')) { 
            if($res[$i] !== 'limite' || $res[$i] !== 'barreira' || $res[$i] !== 'id' || $res[$i] !== 'ativo') { 
                $dia_semana = $data_certa->format('N');
                if($esforco_turma['dia_' . ($dia_semana - 1)] == 1) {                                
                    $esforco_dia += $res[$i]['tempo_medio_diario'];
                } else {
                    $esforco_dia += 0;
                }
            }
        }
    }

    return $esforco_dia;
}

function calcular_esforco_turma($turno) {
    
    global $arrConfig;
    $id_turma = $_GET['id_turma'];
    $filtro_turno = ($turno != -1 ? "AND atividades.id_turno = $turno" : "AND atividades.id_turno = -1" );
    $sql = "SELECT eventos.*, atividades.tempo_sugerido FROM atividades 
    INNER JOIN rel_atividades_turma ON atividades.id = rel_atividades_turma.id_atividade 
    INNER JOIN eventos ON eventos.id = atividades.id_evento
    WHERE rel_atividades_turma.id_turma = $id_turma $filtro_turno";     
            
    $res = my_query($sql);    

    if($turno != -1) {
        $sql = "SELECT eventos.*, atividades.tempo_sugerido FROM atividades 
                INNER JOIN rel_atividades_turma ON atividades.id = rel_atividades_turma.id_atividade 
                INNER JOIN eventos ON eventos.id = atividades.id_evento
                WHERE rel_atividades_turma.id_turma = $id_turma AND atividades.id_turno <> $turno";
        $res_eventos_turma_todas = my_query($sql);
    }

    $eventos_todos_turma = array();
    if($turno != -1) {
        $eventos_todos_turma = array_merge($res, $res_eventos_turma_todas);
        $res = $eventos_todos_turma;
    }
    
    

    
    $sql = "SELECT esforco.* FROM esforco 
    INNER JOIN turma ON turma.id_esforco = esforco.id 
    WHERE turma.id = $id_turma";
    $esforco_turma = my_query($sql);
    $esforco_turma = array_shift($esforco_turma);
    
    $eventos_esforco = array();
    
    $turma_atividade_esforco = [];
    $esforco = -1;
    if(count($res) > 0) {
        //calcular tempo médio diario da atividade        
        for($i = 0; $i < count($res); $i++) {
            $atividade = $res[$i];
            $tempo_medio_diario = 0;
            $comeco = new DateTime($atividade['comeco']);
            $fim = new DateTime($atividade['fim']);
            if($comeco == $fim) {
                $tempo_medio_diario = $atividade['tempo_sugerido'];
            } else {
                $fim->modify('+1 day');
                /* $dias = $comeco->diff($fim)->days; */
                $dias = 0;
                // saber quantos dias eu verdadeiramente tenho, percorrer os dias entre o comeco e o fim, e ver se é dia de trabalho
                $tempo_medio_diario = 0;
                for($j = $comeco; $j < $fim; $j->modify('+1 day')) {
                    $dia_semana = $j->format('N');
                    if($esforco_turma['dia_' . ($dia_semana - 1)] == 1) {
                        $dias++;
                    }
                }
                
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
            $data_certa = $data;
            $data = $data->format('Y-m-d');
            $esforco_dia = calcular_esforco_dia($data, $data_certa, $res, $esforco_turma);            
            $esforco += $esforco_dia;
            $eventos_esforco[$data] = $esforco_dia;
        }
        

        $arr_eventos = array();
        /* pr($eventos_esforco); */
        
        foreach($eventos_esforco as $k => $evento) {
            if($evento >= $esforco_turma['limite']) {
                $arr_eventos[] = array(
                    'title' => '>= ' . $esforco_turma['limite'] . ' horas (' . number_format($evento, 1) . 'h)',
                    'start' => $k,
                    'end' => $k,
                    /* 'backgroundColor' => '#1E3A8A'    */    
                    'extendedProps' => array(
                        'esforco' => '1'
                    )
                );
            } else if($evento >= $esforco_turma['barreira'] && $evento < $esforco_turma['limite']) {
                $arr_eventos[] = array(
                    'title' => '>= ' . $esforco_turma['barreira'] . ' horas (' . number_format($evento, 1) . 'h)',
                    'start' => $k,
                    'end' => $k,
                    'extendedProps' => array(
                        'esforco' => '2'
                    ),

                );
            } else if($evento < $esforco_turma['barreira'] && $evento > 0) {
                $arr_eventos[] = array(
                    'title' => '< ' . $esforco_turma['barreira'] . ' horas (' . number_format($evento, 1) . 'h)',
                    'start' => $k,
                    'end' => $k,
                    'extendedProps' => array(
                        'esforco' => '3'
                    ),
                );
            } else if($evento == 0) {                
            }
        }

        /* pr($arr_eventos); */                
        return $arr_eventos;
    } else {
        return [];
    }        
    
}
function gerar_calendario_atividades($rand, $turno) {    
    $eventos = calcular_esforco_turma($turno);
    $html = gerar_calendario($eventos, 'dayGridMonth', $rand);
    return $html;
}

function gerar_calendario($eventos, $view, $rand) {        
    global $arrConfig;    
    $id_user = $_SESSION['id'];        
    $html = ' 
        
    <div id="ec' . $rand . '"></div>    
    
    <script>            
        let eventos' . $rand . ' = ' . json_encode($eventos) . ';        
        
        let ec' . $rand . ' = new EventCalendar(document.getElementById(\'ec' . $rand . '\'), {
            view: \'' . $view . '\',
            allDaySlot: false,
            eventStartEditable: false,    
            noEventsContent: "Nenhuma atividade criada para mostrar",
            buttonText: {
                today: "Hoje",
                month: "Mês",
                week: "Semana",
                day: "Dia",
                list: "Lista"
            },

            views: {
                listMonth: {                    
                    eventContent: function (arg) {
                    
                    let arrayOfDomNodes = [];
                    let title = document.createElement("t");
                    title.innerHTML =
                    "<div class=\'flex justify-between\'><span>" +
                        arg.event.title +
                        "</span>  - <div>" + (' . $id_user . ' == arg.event.extendedProps.id_professor ? "<a class=\'btn btn-ghost btn-xs\' href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=edicao&tab=agenda&id_evento=" + arg.event.extendedProps.id_evento + "\'><i class=\'fa fa-edit\'></i></a>|<a class=\'btn btn-xs btn-ghost\' href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=details&tab=agenda&id_evento=" + arg.event.extendedProps.id_evento + "\'><i class=\'fa fa-eye\'></i></a>|<a class=\'btn btn-xs btn-ghost\' href=\'' . $arrConfig['url_modules'] . 'trata_remover_atividade.mod.php?id_evento=" + arg.event.extendedProps.id_evento + "' . '\'><i class=\'fa fa-trash\'></i></a>" : "<a href=\'' . $arrConfig['url_admin'] . 'turma.php?id_turma=' . $_GET['id_turma'] . '&tipo=details&tab=agenda&id_evento=" + arg.event.extendedProps.id_evento + "\'><i class=\'fa fa-eye\'></i></a>") +
                        "</div></div><span style=\'font-size: 12px; color: #999\'>Disciplina: " +
                        arg.event.extendedProps.disciplina +
                        " | Tipo: " +  arg.event.extendedProps.tipo + " </span>";
    
                    arrayOfDomNodes.push(title);
                    return { domNodes: arrayOfDomNodes };
                    },
                },
                dayGridMonth: {
                    eventContent: function (arg) {
                      let arrayOfDomNodes = [];
                      let title = document.createElement("t");
                      title.innerHTML = arg.event.title;
      
                      arrayOfDomNodes.push(title);
                      return { domNodes: arrayOfDomNodes };
                    },
                  },
            },

            
            events: eventos' . $rand . ',
        });    

        function clique_editar_evento(id_atv) {
            let url = window.location.href;
            if (url.indexOf(\'?\') > -1){
               url += \'&id_atividade=\' + id_atv;
            

            } else {
               url += \'?id_atividade=\' + id_atv;
            }
            document.getElementById(\'my_modal_5\').dataset.idAtv = id_atv;
            my_modal_5.showModal();
            
        }
        


    </script>
    
    ';
    return $html;
}
?>


