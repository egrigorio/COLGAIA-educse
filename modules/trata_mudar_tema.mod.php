<?php

require_once dirname(__DIR__) . '/bootstrap/management.php';
require_form_post();
$theme = post_string('theme');
if (!in_array($theme, ['mytheme', 'nocas', 'black'], true)) {
    http_response_code(422);
    exit('Tema inválido');
}
$_SESSION['theme'] = $theme;
http_response_code(204);
