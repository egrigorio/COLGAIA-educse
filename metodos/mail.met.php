
<?php
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
/* include '../include/config.inc.php'; */
require_once $arrConfig['dir_site'] . '/vendor/autoload.php';

function enviar_convite_curso($to, $url, $cargo, $curso) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <h1>Convite para o curso</h1>
        <p>Olá, você foi convidado para ser ' . $cargo . ' no curso ' . $curso . ' na educse. Para aceitar o convite, clique no link abaixo.</p>
        <a href="' . $url . '">clique aqui para acessar o curso</a>
    </body>
    </html>';
    $alt_body = 'Convite para a educse';
    $assunto = 'Convite para a educse';
    enviar_email($to, '', $assunto, $alt_body, $html );
}

function enviar_convite_plataforma($to, $url, $cargo, $curso) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <h1>Convite para a plataforma</h1>
        <p>Olá, você foi convidado para ser ' . $cargo . ' no curso ' . $curso . ' na educse. Para aceitar o convite, clique no link abaixo.</p>
        <a href="' . $url . '">clique aqui para acessar a plataforma</a>
    </body>
    </html>';
    $alt_body = 'Convite para a educse';
    $assunto = 'Convite para a educse';
    enviar_email($to, '', $assunto, $alt_body, $html );
}

function recuperar_senha($to, $nome, $url) {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <h1>recuperar password</h1>
        <a href="' . $url . '">clique aqui para recuperar sua password</a>
    </body>
    </html>';
    $alt_body = 'Recuperar password';
    $assunto = 'Recuperar password';
    enviar_email($to, $nome, $assunto, $alt_body, $html );
    
}
function email_verificacao($to, $nome, $codigo) {
    
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <h1>Validação do código</h1>
        <p>Insira o seguinte código na tela do site para concluir o processo de verificação: ' . $codigo . '</p>
    </body>
    </html>';
    $alt_body = 'Validação do código: ' . $codigo;
    $assunto = 'Validação do código';
    enviar_email($to, $nome, $assunto, $alt_body, $html );
    return $codigo;
};

function enviar_email($to, $nome, $assunto, $alt_body, $html){
    $user = "noreply.educse@gmail.com";
    $pass = "vxsugijpioiwdiee";
    
    
    $mail = new PHPMailer(true);
     
    try {
        $mail->SMTPDebug = 0;                                       
        $mail->isSMTP();                                            
        $mail->Host       = "smtp.gmail.com";                    
        $mail->SMTPAuth   = true;                             
        $mail->Username   = $user;   
        $mail->CharSet = 'UTF-8';              
        $mail->Password   = $pass;                        
        $mail->SMTPSecure = 'ssl';                              
        $mail->Port       = 465;       
        $mail->setFrom( $user, 'Educse');           
        $mail->addAddress($to, $nome);                  
        $mail->isHTML(true);                                  
        $mail->Subject = $assunto;
        $mail->Body    = $html;
        $mail->AltBody = $alt_body;
        $mail->send();
        
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

};


?>