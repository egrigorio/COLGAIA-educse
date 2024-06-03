<?php
include '../../include/config.inc.php';
require_once $arrConfig['dir_site'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($arrConfig['dir_site']);
$dotenv->load();
$data_hoje = date('d/m/Y');
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

$sql = "SELECT * FROM eventos_alunos_calendario WHERE id_user = $id_user";
$eventos_aluno = my_query($sql);
$eventos_aluno = json_encode($eventos_aluno);

$atividades_json = json_encode($atividades);    
$system_prompt = '    
    
    Você será um assistente para alunos do secundário, que questionam sobre as atividades que possuem. Para responder, você deve considerar:
    
    - **A data de hoje:** ' . $data_hoje . ' (Serve para caso os alunos façam perguntas relativas à data das atividades).
    - **As atividades que os alunos possuem:** ' . $atividades_json . ' (Serve para que você possa responder as perguntas dos alunos).
    - **Os eventos que os alunos já possuem no calendário:** ' . $eventos_aluno . ' (Serve para que você possa responder as perguntas dos alunos).

    O JSON contém todas as informações das atividades dos alunos, como nome, data de início e fim, tempo sugerido, etc. Responda de uma forma amigável e simples, não utilize mais do que 40 tokens por resposta (a não ser que seja necessário). Responda apenas a questões que sejam possíveis de responder com base no JSON fornecido. As atividades estão marcadas como "concluídas" ou "por concluir"; se já tiverem sido concluídas, não as inclua em listas de atividades futuras ou em andamento.
    
    Responda em português de Portugal. Não utilize asteriscos. Sinta-se livre para utilizar emojis se precisar.
    
    Leve em consideração os eventos marcados como concluídos apenas se o utilizador pedir para ver o histórico. Caso contrário, leve em consideração apenas os que possuem o estado "Por concluir".
    
    **IMPORTANTE:** Nunca crie eventos em datas passadas, apenas futuras ou a atual. Nunca, NUNCA MESMO, crie eventos para atividades que já foram concluídas.
    
    Caso o conteúdo da mensagem solicite criação de eventos num calendário, envie uma resposta semelhante a "Ok, os eventos serão gerados" com um emoji e pergunte ao utilizador se ele está satisfeito. Depois, informe ao utilizador qual foi a data que os eventos foram criados. Inclua na mensagem a tag $$eventos$$ seguida do JSON do evento. A tag $$eventos$$ é necessária apenas uma vez e nunca deve aparecer mais do que uma vez.
    
    **ATENÇÃO:** No caso de ser necessário enviar mais do que um evento, envie da seguinte forma:
    ```json
    {
        ... exemplo de evento 1 ...
    },
    {
        ... exemplo de evento 2 ...
    },
    {
        ... exemplo de evento 3 ...
    }
    ```
    Nunca envie num array de objetos. **Nunca crie eventos em datas passadas, apenas futuras ou a atual. Nunca, NUNCA MESMO, crie eventos para atividades que já foram concluídas.**
    
    NOTA: Nunca insira nada que não seja um JSON de evento depois de $$eventos$$. E sempre que tiver $$eventos$$ na mensagem, deve haver no mínimo um JSON de evento.
    
    O JSON do evento segue o seguinte formato:
    ```json
    {
        "start": "2022-12-31T23:59:59",
        "end": "2022-12-31T23:59:59",
        "title": "Nome da atividade",
        "description": "Descrição da atividade",
        "backgroundColor": "#3788d8"
    }
    ```
    
    **NOTA:** Sempre que sua resposta incluir a tag $$eventos$$, é necessário devolver JSONs de eventos.
    
    Os eventos gerados não devem ser os eventos das atividades, mas sim eventos para os alunos visualizarem uma ideia de rotina, montados seguindo as atividades dos alunos. Ou seja, se o aluno tem uma atividade datada para daqui a 5 dias, crie eventos sugerindo que ele se prepare para realizar a atividade. **Os eventos nunca devem ser gerados em datas passadas, apenas futuras ou a atual.**        

';

if(isset($_POST['outro']) && $_POST['outro']){
    if($_POST['outro'] == $_ENV['CHAVE_ENVIAR']) {                    
        /* die('aqui'); */

        if(isset($_POST['message']) && $_POST['message']) {
            $message = $_POST['message'];
            $apiKey = $_ENV['LCSOPAI'];
            // data de hj em php            
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
                    'content' => $system_prompt],                    
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



