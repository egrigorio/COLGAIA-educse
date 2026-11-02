<?php

// Ponto de compatibilidade para as páginas existentes.
require_once dirname(__DIR__) . '/bootstrap/app.php';
$arrConfig ??= require dirname(__DIR__) . '/config/app.php';
require_once __DIR__ . '/db.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once dirname(__DIR__) . '/metodos/mail.met.php';
require_once dirname(__DIR__) . '/admin/dashboards/layout.dash.php';
