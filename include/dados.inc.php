<?php

function def_arrCampos($modulo, &$arrCampos)
{
    switch ($modulo) {
        case 'curso':
            $arrCampos = array(
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
                'nome_curso' => array(
                    'legenda' => 'Nome do Curso',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1,
                    'unique' => 1,
                ),
                'abreviatura' => array(
                    'legenda' => 'Abreviatura',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1,
                    'unique' => 1,

                ),
                'duracao' => array(
                    'legenda' => 'Duração',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'id_diretor_curso' => array(
                    'legenda' => 'ID do Diretor do Curso',
                    'tipo' => 'escondido',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,                    
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0,
                    'unique' => 1,
                    'default' => $_SESSION['id'], // significa que vai pegar o id do diretor do curso que está criando o curso
                ),
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'turma':
            $arrCampos = array(                
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
                'nome_turma' => array(
                    'legenda' => 'Nome da turma',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1,
                    'unique' => 1,
                ),
                'id_curso' => array(
                    'legenda' => 'ID do Curso',
                    'tipo' => 'select',
                    'carrega_opcoes' => array (
                        'tabela' => 'curso',
                        'chave' => 'id',
                        'legenda' => 'nome_curso',
                        'ativo' => 'ativo',
                        'null' => '1',
                        'null_legenda' => 'Escolha um curso',
                        'null_valor' => 0,
                        'null_valor_legenda' => '---'
                    ),
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'ano_letivo' => array(
                    'legenda' => 'Ano letivo',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'id_esforco' => array(
                    'legenda' => 'ID do Esforço',
                    'tipo' => 'escondido',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1,
                    'default' => 0,
                ),
                'id_diretor_turma' => array(
                    'legenda' => 'ID do DT',
                    'tipo' => 'escondido',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1,
                    'default' => $_SESSION['id'],
                ),
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'ativo' => array(
                    'legenda' => 'Ativo',
                    'tipo' => 'escondido',
                    'opcoes' => array('0' => 'Não', '1' => 'Sim'),
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 10,
                    'required' => 0,
                    'default' => 1
                ),

            );
            break;
        case 'turno':
            $arrCampos = array(                
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
                'id_turma' => array(
                    'legenda' => 'ID da turma',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'numero' => array(
                    'legenda' => 'Número do turno',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),                
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'atividades':
            $arrCampos = array(
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
                'id_evento' => array(
                    'legenda' => 'ID do Evento',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'descricao' => array(
                    'legenda' => 'Descrição',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),                
                'tipo' => array(
                    'legenda' => 'Tipo',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'id_professor' => array(
                    'legenda' => 'ID do Professor',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'tempo_sugerido' => array(
                    'legenda' => 'Tempo sugerido',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'id_disciplina' => array(
                    'legenda' => 'ID da Disciplina',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'disciplinas':
            $arrCampos = array(
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
                'nome' => array(
                    'legenda' => 'Nome da disciplina',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'abreviatura' => array(
                    'legenda' => 'Abreviatura',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),                
                'duracao' => array(
                    'legenda' => 'Duração',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),                
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'esforco':
            $arrCampos = array(
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
                'limite' => array(
                    'legenda' => 'Limite de horas',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'barreira' => array(
                    'legenda' => 'Avisar quando ultrapassar',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'dia_0' => array(
                    'legenda' => 'Domingo',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_1' => array(
                    'legenda' => 'Segunda-feira',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_2' => array(
                    'legenda' => 'Terça-feira',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_3' => array(
                    'legenda' => 'Quarta-feira',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_4' => array(
                    'legenda' => 'Quinta-feira',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_5' => array(
                    'legenda' => 'Sexta-feira',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'dia_6' => array(
                    'legenda' => 'Sábado',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 0,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'eventos':
            $arrCampos = array(
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
                'titulo' => array(
                    'legenda' => 'Titulo do evento',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'barreira' => array(
                    'legenda' => 'Avisar quando ultrapassar',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'comeco' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'fim' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'instituicao':
            $arrCampos = array(
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
                'nome' => array(
                    'legenda' => 'Nome da instituição',
                    'tipo' => 'varchar',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'criado_em' => array(
                    'legenda' => 'Criado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 0
                ),
                'editado_em' => array(
                    'legenda' => 'Editado em:',
                    'tipo' => 'datetime',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 0,
                    'editar' => 1,
                    'largura' => 100,
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
        case 'rel_user_curso': 
            $arrCampos = array(
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
                'id_user' => array(
                    'legenda' => 'ID do Utilizador',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
                'id_curso' => array(
                    'legenda' => 'ID do Curso',
                    'tipo' => 'int',
                    'chave' => 0,
                    'listagem' => 1,
                    'inserir' => 1,
                    'editar' => 1,
                    'largura' => 100,
                    'required' => 1
                ),
            );
    }
};
