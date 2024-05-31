<?php
include '../../include/config.inc.php';
require_once $arrConfig['dir_site'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($arrConfig['dir_site']);
$dotenv->load();

if(isset($_POST['outro']) && $_POST['outro']){
    if($_POST['outro'] == $_ENV['CHAVE_ENVIAR']) {
        $id_user = $_POST['user'];
        $id_turma = $_POST['id_turma'];
        
        $sql = "SELECT eventos.*, atividades.*, users.username AS nome_professor, turno.numero AS numero_turno, disciplinas.nome AS nome_disciplina FROM atividades
        INNER JOIN rel_atividades_turma ON rel_atividades_turma.id_atividade = atividades.id
        INNER JOIN eventos ON eventos.id = atividades.id_evento
        INNER JOIN users ON atividades.id_professor = users.id
        INNER JOIN turno ON atividades.id_turno = turno.id
        INNER JOIN disciplinas ON disciplinas.id = atividades.id_disciplina
        WHERE rel_atividades_turma.id_turma = $id_turma";
        $atividades_com_num_turno = my_query($sql);

        $sql = "SELECT eventos.*, atividades.*, users.username AS nome_professor, disciplinas.nome AS nome_disciplina FROM atividades
        INNER JOIN rel_atividades_turma ON rel_atividades_turma.id_atividade = atividades.id 
        INNER JOIN eventos ON eventos.id = atividades.id_evento
        INNER JOIN users ON atividades.id_professor = users.id  
        INNER JOIN disciplinas ON disciplinas.id = atividades.id_disciplina      
        WHERE rel_atividades_turma.id_turma = $id_turma";
        $atividades_todas = my_query($sql);
        
        
        $atividades = [];
        foreach($atividades_com_num_turno as $a) {
            $atividades[] = $a;
        }
        foreach($atividades_todas as $a) {
            // validar se o id do a já está no atividades
            $existe = false;
            foreach($atividades as $at) {
                if($at['id'] == $a['id']) {
                    $existe = true;
                }
            }
            if(!$existe) {
                $atividades[] = $a;
            }            
        }
        
        foreach($atividades as $key => $atividade) {
            if($atividade['fim'] < date('Y-m-d')) {
                $atividades[$key]['estado'] = 'Concluída';
            } else {
                $atividades[$key]['estado'] = 'Por concluir';
            }
        }
        
        $atividades_json = json_encode($atividades);                
        /* die('aqui'); */

        if(isset($_POST['message']) && $_POST['message']) {
            $message = $_POST['message'];
            $apiKey = $_ENV['LCSOPAI'];
            // data de hj em php
            $data_hoje = date('d/m/Y');
            $message_atividades = '<date> ' . $data_hoje . ' <date> <context>: ' . $atividades_json . ' <context> """ ' . $message . ' """';        
            /* echo $message_atividades;
            die('aqui'); */
            $yourApiKey = $apiKey;
            $client = OpenAI::client($yourApiKey);
            
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                /* 'model' => 'gpt-4o', */
                'messages' => [
                    ['role' => 'system',
                    'content' => '
                    Você será um assistente para alunos do secundário, que questionam sobre as atividades que possuem. Para responder, você deve
                    levar em consideração alguns fatores, sendo eles:
                    - A data de hoje: ' . $data_hoje . ' (Serve para caso os alunos façam perguntas relativas a data das atividades);
                    - As atividades que os alunos possuem: ' . $atividades_json . ' (Serve para que você possa responder as perguntas dos alunos);                                        
                    
                    O JSON contém todas as informações das atividades dos alunos, como nome, data de inicio e data de fim, tempo sugerido etc.
                    Responda de uma forma amigável e simples, não utilize mais do que 40 tokens por resposta (apenas se necessário).
                    Responda apenas a questões que sejam possíveis de responder tendo por base o JSON fornecido.
                    As atividades estão marcadas como concluídas ou por concluir, se já tiverem sido concluídas, não as inclua em listas de atividades futuras,
                    ou atividades em andamento.
                    Responda em português de Portugal.
                    Não utilize asteriscos.
                    Sinta-se livre para utilizar emojis se preciso.

                    Você só deve levar em consideração os eventos marcados como concluídos, no caso de o utilizador pedir por, por exemplo, pra ver o histórico. Caso contrário,
                    leve em consideração apenas os que possuem o estado "Por concluir".
                    

                    Caso o conteúdo da mensagem solicite criação de eventos num calendário, envie resposta semelhantes a "Ok, os eventos serão gerados" adicione um emoji e pergunte ao 
                    utilizador se ele está satisfeito, depois informe ao user qual foi a data que os eventos foram criados.
                    Depois disso, inclua na mensagem $$eventos$$ seguido do JSON do evento. A tag $$eventos$$ é necessária apenas uma vez, nunca deve aperecer mais do que uma vez.
                    ATENÇÃO: No caso de ser neccessário enviar mais do que um evento, envie da seguinte forma:
                        {
                            ... exemplo de evento 1 ...
                        },
                        {
                            ... exemplo de evento 2 ...
                        },
                        {
                            ... exemplo de evento 3 ...
                        }
                        ...
                    isto é, nunca envie num array de objetos.
                    NOTA: NUNCA INSIRA NADA QUE NÃO SEJA UM JSON DE EVENTO DEPOIS DE $$eventos$$. E SEMPRE QUE TIVER $$eventos$$ NA MENSAGEM, DEVE TER NO MINIMO UM JSON DE EVENTO.
                    O JSON do evento, segue o seguinte formato:
                    {
                        "start": "2022-12-31T23:59:59",
                        "end": "2022-12-31T23:59:59",
                        "title": "Nome da atividade",
                        "description": "Descrição da atividade",
                        "backgroundColor": "#3788d8",
                    }
                    
                    
                    NOTA: Sempre que sua resposta incluir a tag $$eventos$$ é necessário devolver JSONS de eventos.

                    Os eventos que são gerados, não devem ser os eventos das atividades, e sim eventos para os alunos visualizarem uma ideia de rotina,
                    que são montados seguindo as atividades dos alunos, ou seja, se o aluno tem uma atividade datada para daqui 5 dias, crie eventos sugerindo 
                    ele se preparar para realizar a atividade.
                    Os eventos nunca devem ser gerados em datas passadas, isto é, menores que a data atual, apenas futuras ou a atual.

                    
                    

                    
                    
                    '
                    ],
                    ['role' => 'user', 'content' => $message],
                ],
                /* 'max_tokens' => 1, */
                
            ]);
                    
            echo $result->choices[0]->message->content;
            /* echo json_encode($result->choices[0]->message->content);      */       
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=1');
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=1');
    }
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=1');
}



