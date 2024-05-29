<?php
include '../../include/config.inc.php';
require_once $arrConfig['dir_site'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($arrConfig['dir_site']);
$dotenv->load();

if(isset($_POST['outro']) && $_POST['outro']){
    if($_POST['outro'] == $_ENV['CHAVE_ENVIAR']) {
        $id_user = $_POST['user'];
        $id_turma = $_POST['id_turma'];
        
        $sql = "SELECT eventos.*, atividades.*, users.username AS nome_professor, turno.numero AS numero_turno FROM atividades
                INNER JOIN rel_atividades_turma ON rel_atividades_turma.id_atividade = atividades.id
                INNER JOIN eventos ON eventos.id = atividades.id_evento
                INNER JOIN users ON atividades.id_professor = users.id
                INNER JOIN turno ON atividades.id_turno = turno.id
                WHERE rel_atividades_turma.id_turma = $id_turma";
        $atividades_com_num_turno = my_query($sql);

        $sql = "SELECT eventos.*, atividades.*, users.username AS nome_professor FROM atividades
        INNER JOIN rel_atividades_turma ON rel_atividades_turma.id_atividade = atividades.id 
        INNER JOIN eventos ON eventos.id = atividades.id_evento
        INNER JOIN users ON atividades.id_professor = users.id        
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
        
        
        $atividades_json = json_encode($atividades);                
        /* die('aqui'); */

        if(isset($_POST['message']) && $_POST['message']) {
            $message = $_POST['message'];
            $apiKey = $_ENV['LCSOPAI'];
            // data de hj em php
            $data_hoje = date('d/m/Y');
            $message_atividades = '{%%DATA_HOJE%%} ' . $data_hoje . ' {%%DATA_HOJE%%} {%%CONTEXT%%}: ' . $atividades_json . ' {%%CONTEXT%%} {%%MESSAGE%%}: ' . $message . ' {%%MESSAGE%%}';        
            
            $yourApiKey = $apiKey;
            $client = OpenAI::client($yourApiKey);
            
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => [
                    ['role' => 'system',
                    'content' => 'o prompt está estruturado de forma que você sempre tenha o context, isto é, a parte da mensagem envolvida entre {%%CONTEXT%%}. O {%%DATA_HOJE%%} é a data de hoje e o {%%MESSAGE%%} é o que o user enviou.
                    sua resposta será exibida em um navegador, por isso utilize elementos html para poder estruturar sua resposta como bem entender, sempre a colocando dentro de um elemento <span>, e se for preciso estilizar, utilize as classes do tailwindcss. 
                    responda em português de portugal, sempre em português.
                    se sentir necessidade, esteja livre para utilizar emojis.
                    Tente sintetizar sua resposta, fornecendo o máximo de informações possíveis, mas de forma resumida. Tente utilizar no máximo 40 tokens por resposta, se preciso pode ultrapassar, mas tente sempre usar o mínimo possível.
                    Nunca faça o trabalho pros alunos.
                    caso no prompt o turno seja -1, significa que a atividade é pra turma toda, caso tenha um número, é para um turno específico.
                    '
                    ],
                    ['role' => 'user', 'content' => $message_atividades],
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



