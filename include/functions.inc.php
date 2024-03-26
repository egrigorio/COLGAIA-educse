<?php

function pr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function logs() {
    global $tipoLog;
    $arrUrl = explode('/', $_SERVER['REQUEST_URI']);
    $file = $arrUrl[count($arrUrl) - 1];

    switch($file) {
        case 'tratalogin.inc.php':
            $_SESSION['tipoLog'] = 'Login';
            break;
        case 'logout.php': 
            $_SESSION['tipoLog'] = 'Logout';
            break;
        default: 
            $_SESSION['tipoLog'] = '';
            
    };

    $dataHora = date('Y-m-d H:i:s');
    $url = $_SERVER['REQUEST_URI'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $idUser = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

    $session = session_id();
    
    $sql = "INSERT INTO logs (dataHora, url, ip, idUser, session, tipoLog) VALUES ('$dataHora', '$url', '$ip', $idUser, '$session', '$tipoLog')";
    $result = my_query($sql);

};