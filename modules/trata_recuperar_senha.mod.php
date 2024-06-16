<?php

include '../include/config.inc.php';

pr($_POST);
if($_POST['tipo'] == "1") {
    // primeira parte do recuperar senha, isto é, aqui vou ter o email da conta que deseja recuperar
    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = my_query($sql);
    if($res == 0) {
        echo "Email não encontrado";
        exit;
    } else {
        // gerar token 
        $token = md5(uniqid(rand(), true));
        $_SESSION['token_password'] = $token;
        $_SESSION['email_user_recuperar_pass'] = $email;
        $url = $arrConfig['url_paginas'] . "auth/recuperar_senha.php?token=$token";
        recuperar_senha($email, $res[0]['username'], $url);
        $_SESSION['msg_sucesso'] = "Email enviado com sucesso";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    

} else {
    // segunda parte do recuperar senha, isto é, aqui vou ter o código que foi enviado para o email
    if(isset($_POST['token']) && $_POST['token'] == $_SESSION['token_password']){
        
        $password = $_POST['password'];
        $confirmar_password = $_POST['password2'];
        $email = $_SESSION['email_user_recuperar_pass'];
        if($password != $confirmar_password) {
            echo "As passwords não coincidem";
            exit;
        }
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$pass' WHERE email = '$email'";
        my_query($sql);
        echo "Password alterada com sucesso";
        $_SESSION['msg_sucesso'] = "Password alterada com sucesso";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        $_SESSION['erro'] = "Token inválido";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }


}