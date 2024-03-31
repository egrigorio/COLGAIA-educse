<?php
include '../include/config.inc.php';
include_once $arrConfig['dir_include'] . 'auth.inc.php';
include_once $arrConfig['dir_include'] . 'functions.inc.php';

function def_config_adm($modulo, &$arr_config)
{
    switch ($modulo) {
        case 'hero_turma':
            $arr_config = array(
                'titulo' => 'nome_turma',
                'subtitulo' => 'diretor'
            );
            break;
        case 'tabs_curso': 
            $arr_config = array(
                'tab1' => array(
                    'label' => 'Customização',
                    'content' => 'Tab content 1',
                    'checked' => 1,
                    'name' => 'tabs_curso'
                ),
                'tab2' => array(
                    'label' => 'Configurações',
                    'content' => 'Tab content 2',
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
                'tab3' => array(
                    'label' => 'Alunos',
                    'content' => 'Tab content 3',
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
                'tab4' => array(
                    'label' => 'Professores',
                    'content' => professores_tabs_cursos(),
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
            );
            break;
            case 'tabs_direcao_turma': 
                $arr_config = array(
                    'tab1' => array(
                        'label' => 'Customização',
                        'content' => 'Tab content 1',
                        'checked' => 1,
                        'name' => 'tabs_dt'
                    ),
                    'tab2' => array(
                        'label' => 'Configurações',
                        'content' => 'Tab content 2',
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab3' => array(
                        'label' => 'Alunos',
                        'content' => 'Tab content 3',
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                );
                break;
                case 'tabs_turma': 
                    $arr_config = array(
                        'tab1' => array(
                            'label' => 'Customização',
                            'content' => 'Tab content 1',
                            'checked' => 1,
                            'name' => 'tabs_dt'
                        ),
                        'tab2' => array(
                            'label' => 'Configurações',
                            'content' => 'Tab content 2',
                            'checked' => 0,
                            'name' => 'tabs_dt'
                        ),
                        'tab3' => array(
                            'label' => 'Alunos',
                            'content' => 'Tab content 3',
                            'checked' => 0,
                            'name' => 'tabs_dt'
                        ),
                    );
                    break;
    }
};
