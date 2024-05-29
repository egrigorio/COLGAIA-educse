<?php
/* include '../include/config.inc.php'; */
include_once $arrConfig['dir_include'] . 'functions.inc.php';

function def_config_adm($modulo, &$arr_config)
{
    switch ($modulo) {
        case 'users':
            $arr_config = array(
                'id' => array(
                    'legenda' => 'ID',
                    'tipo' => 'int',
                    'chave' => 1,
                    'listagem' => 0,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'pfp' => array(
                    'legenda' => 'Foto de Perfil',
                    'tipo' => 'img',
                    'chave' => 1,
                    'listagem' => 0,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'username' => array(
                    'legenda' => 'Username',
                    'tipo' => 'varchar',
                    'chave' => 1,
                    'listagem' => 0,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'email' => array(
                    'legenda' => 'Email',
                    'tipo' => 'varchar',
                    'chave' => 1,
                    'listagem' => 0,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'password' => array(
                    'legenda' => 'Password',
                    'tipo' => 'varchar',
                    'chave' => 1,
                    'listagem' => 0,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'cargo' => array(
                    'legenda' => 'Cargo',
                    'tipo' => 'varchar',
                    'chave' => 1,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 50,
                    'required' => 1
                ),
                'criado_em' => array(
                    'legenda' => 'Criado em',
                    'tipo' => 'datetime',
                    'chave' => 1,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em',
                    'tipo' => 'datetime',
                    'chave' => 1,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'ultimo_login' => array(
                    'legenda' => 'Último Login',
                    'tipo' => 'datetime',
                    'chave' => 1,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 0,
                    'largura' => 50,
                    'required' => 0
                ),
                'ativo' => array(
                    'legenda' => 'Ativo',
                    'tipo' => 'int',
                    'opcoes' => array('0' => 'Não', '1' => 'Sim'),
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 10,
                    'required' => 1
                ),

            );
            break;
        case 'hero_turma':
            $arr_config = array(
                'titulo' => 'nome_turma',
                'subtitulo' => 'diretor'
            );
            break;
        case 'tabs_curso': 
            $arr_config = array(
                /* 'tab1' => array(
                    'label' => 'Customização',
                    'content' => 'Tab content 1',
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),            */     
                'tab2' => array(
                    'label' => 'Disciplinas',
                    'content' => disciplinas_tabs_cursos(),
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
                'tab3' => array(
                    'label' => 'Alunos',
                    'content' => alunos_tabs_cursos(),
                    'checked' => 1,
                    'name' => 'tabs_curso'
                ),
                'tab4' => array(
                    'label' => 'Professores',
                    'content' => professores_tabs_cursos(),
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
                'tab5' => array(
                    'label' => 'Turmas',
                    'content' => painel_gestao_turmas_diretor_curso(),
                    'checked' => 0,
                    'name' => 'tabs_curso'
                ),
            );
            break;
            case 'tabs_direcao_turma': 
                $arr_config = array(
                    'tab0' => array(
                        'label' => 'Dashboard',
                        'content' => painel_direcao_turma(),
                        'checked' => 1,
                        'name' => 'tabs_dt'
                    ),
                    'tab1' => array(
                        'label' => 'Professores',
                        'content' => tabela_vista_professores_turma(true),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab2' => array(
                        'label' => 'Turno',
                        'content' => tabela_turnos_diretor_turma(),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab3' => array(
                        'label' => 'Alunos',
                        'content' => tabela_alunos_diretor_turma(),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab4' => array(
                        'label' => 'Esforço',
                        'content' => esforco_direcao_turma(),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab5' => array(
                        'label' => 'Atividade',
                        'content' => criar_atividade_turma(),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),
                    'tab6' => array(
                        'label' => 'Agenda',
                        'content' => agenda_turma(),
                        'checked' => 0,
                        'name' => 'tabs_dt'
                    ),

                );
                break;
                case 'tabs_turma': 
                    $arr_config = array(
                        /* 'tab1' => array(
                            'label' => 'Customização',
                            'content' => 'Tab content 1',
                            'checked' => 0,
                            'name' => 'tabs_turma'
                        ), */
                        'tab2' => array(
                            'label' => 'Professores',
                            'content' => tabela_vista_professores_turma(),
                            'checked' => 0,
                            'name' => 'tabs_turma'
                        ),
                        'tab3' => array(
                            'label' => 'Alunos',
                            'content' => tabela_vista_alunos_turma(),
                            'checked' => 0,
                            'name' => 'tabs_turma'
                        ),
                        'tab4' => array(
                            'label' => 'Criar Atividade',
                            'content' => criar_atividade_turma(),
                            'checked' => 1,
                            'name' => 'tabs_turma'
                        ),
                        'tab5' => array(
                            'label' => 'Agenda',
                            'content' => agenda_turma(),
                            'checked' => 0,
                            'name' => 'tabs_turma'
                        ),
                    );
                    break;
                case 'tabs_instituicao':         
                    $arr_config = array(
                        'tab1' => array(
                            'label' => 'Disciplinas',
                            'content' => tabela_disciplinas_instituicao(),
                            'checked' => 0,
                            'name' => 'tabs_instituicao'
                        ),
                        'tab2' => array(
                            'label' => 'Diretores de Curso',
                            'content' => tabela_diretores_curso_instituicao(),
                            'checked' => 0,
                            'name' => 'tabs_instituicao'
                        ),
                        'tab3' => array(
                            'label' => 'Cursos',
                            'content' => tabela_cursos_instituicao(),
                            'checked' => 1,
                            'name' => 'tabs_instituicao'
                        ),                        
                    );
                    break;
                case 'tabs_aluno': 
                    $arr_config = array(
                        'tab1' => array(
                            'label' => 'Assistente',
                            'content' => 'AI assistente do aluno',
                            'checked' => 0,
                            'name' => 'tabs_aluno'
                        ),
                        'tab2' => array(
                            'label' => 'Professores',
                            'content' => tabela_vista_professores_turma(),
                            'checked' => 0,
                            'name' => 'tabs_aluno'
                        ),
                        'tab3' => array(
                            'label' => 'Alunos',
                            'content' => tabela_vista_alunos_turma(),
                            'checked' => 0,
                            'name' => 'tabs_aluno'
                        ),                        
                        'tab5' => array(
                            'label' => 'Agenda',
                            'content' => agenda_turma(),
                            'checked' => 1,
                            'name' => 'tabs_aluno'
                        ),   
                    );
                    break;
    }
};
