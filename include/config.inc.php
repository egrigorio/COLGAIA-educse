<?php 

@session_start();
global $arrConfig;

if($_SERVER["HTTP_HOST"] == 'web.colgaia.local' || $_SERVER["HTTP_HOST"] == 'localhost' ){
    error_reporting(E_ALL);
} else {
    error_reporting(0);
};  

$arrConfig['servername'] = 'localhost';
$arrConfig['username'] = 'root';
$arrConfig['password'] = '';
$arrConfig['dbname'] = 'educse';

$arrConfig['isLoginKey'] = 'aajbajkh%/rcKI!ª~uaca76';

$arrConfig['url_site'] = 'http://' . $_SERVER["HTTP_HOST"] . '/educse';
$arrConfig['dir_site'] = '/Applications/XAMPP/xamppfiles/htdocs/educse'; 
$arrConfig['url_paginas'] = $arrConfig['url_site'] . '/pages/';
$arrConfig['url_modules'] = $arrConfig['url_site'] . '/modules/';
$arrConfig['dir_modules'] = $arrConfig['dir_site'] . '/modules/';
$arrConfig['url_admin'] = $arrConfig['url_site'] . '/admin/';
$arrConfig['dir_admin'] = $arrConfig['dir_site'] . '/admin/';


$arrConfig['dir_include'] = $arrConfig['dir_site'] . '/include/';

$arrConfig['email_token'] = 'aajbajkh%/rcKI!ª~uaca76';
$arrConfig['key_jwt'] = 'r/FqiRRE';
$arrConfig['encode_jwt'] = 'IJt3d80e';

$arrConfig['url_public'] = $arrConfig['url_site'] . '/public/';
$arrConfig['url_pfp'] = $arrConfig['url_site'] . '/public/pfp/';
$arrConfig['dir_uploads'] = $arrConfig['dir_site'] . '/uploads/';
$arrConfig['url_uploads'] = $arrConfig['url_site'] . '/uploads/';
$arrConfig['dir_img'] = $arrConfig['dir_site'] . '/images/';
$arrConfig['url_img'] = $arrConfig['url_site'] . '/images/';
$arrConfig['auth_imgType'] = ['image/jpeg', 'image/png', 'image/gif'];
include_once $arrConfig['dir_include'] . 'functions.inc.php';
include_once $arrConfig['dir_include'] . 'db.inc.php';
include_once $arrConfig['dir_site'] . '/metodos/mail.met.php';
include_once $arrConfig['dir_admin'] . 'dashboards/layout.dash.php';

logs();