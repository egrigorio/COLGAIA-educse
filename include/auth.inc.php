<?php

require_once __DIR__ . '/config.inc.php';
if (!isset($_SESSION['id'])) {
    redirect_to($arrConfig['url_paginas'] . 'auth/login.php');
}
