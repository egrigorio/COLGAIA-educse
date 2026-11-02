# Banco reconstruído

O repositório não continha dump SQL, migrações ou definições de views. `database/schema.sql` foi reconstruído a partir de `INSERT`, `SELECT`, `UPDATE`, joins, formulários e metadados de `include/dados.inc.php`.

O instalador só aceita um banco vazio. Não é uma migração de um banco antigo, não apaga tabelas e não recupera dados históricos. DDL MySQL não é transacional: se a instalação falhar a meio, inspecione o erro e use outro banco vazio depois de corrigir a causa.

## Estrutura inferida

| Grupo | Tabelas |
| --- | --- |
| Contas | `users`, `conf_convite`, `logs` |
| Organização escolar | `instituicao`, `curso`, `turma`, `disciplinas`, `turno` |
| Planeamento | `esforco`, `eventos`, `atividades`, `eventos_alunos_calendario` |
| Relações | `rel_user_curso`, `rel_turma_user`, `rel_turno_user`, `rel_instituicao_curso`, `rel_instituicao_disciplinas`, `rel_disciplina_curso`, `rel_disciplina_curso_ano`, `rel_disciplina_turma`, `rel_disciplina_user`, `rel_atividades_turma` |

As quatro views utilizadas pelo código ativo foram reconstruídas:

- `view_user_curso`: contas e inscrições em cursos, incluindo estado e diretor.
- `view_user_turma_turno`: inscrições em turmas e turnos; ausência de turno aparece como id -1 e número 0.
- `view_turno_turma`: turnos distintos associados às inscrições ativas de uma turma.
- `view_professor_turno_turma`: professores com inscrição ativa e respetivos turnos.

Nomes de views presentes apenas em comentários antigos não foram recriados. A referência `rel_user_turma` foi corrigida para `rel_turma_user`, que é o nome utilizado pelo resto da aplicação.

## Decisões que exigem conhecimento do projeto original

- Os tipos, comprimentos, índices e valores por omissão são inferidos. Nomes e relações têm evidência no PHP; não existe prova dos tipos exatos do banco original.
- `esforco.limite=4` e `barreira=3` horas são valores iniciais de demonstração, editáveis pela aplicação. Dias úteis começam ativos e fins de semana inativos.
- `dia_0` representa segunda-feira e `dia_6`, domingo, seguindo os cálculos e o controlador de esforço. As legendas antigas do formulário genérico foram alinhadas com essa convenção.
- `turma.ano` guarda o índice do ano no curso, começando em 1; `ano_letivo` usa o formato `2026/27`.
- `turno.numero` é global: o código procura e reutiliza turnos pelo número. A associação à turma passa por `rel_turma_user` e `rel_turno_user`; `turno.id_turma` é mantido opcional devido aos metadados antigos.
- Os ids são inteiros com sinal porque o código usa -1 para diretor ou turno não atribuído. `atividades.id_turno` também permite NULL, como o controlador original.
- As relações têm índices, mas ainda não têm foreign keys: vários controladores apagam pais antes dos filhos e usam -1 como sentinela. Acrescentar restrições sem corrigir esses fluxos quebraria a compatibilidade. É uma dívida técnica explícita, não uma garantia de integridade referencial.
- `rel_user_curso.estado` é texto: o código usa tanto `1` como `Convite enviado`.
- `users.email` e `users.username` são únicos, em coerência com a validação do cadastro. Os demais vínculos não ganharam restrições de unicidade sem evidência suficiente.
- Campos de auditoria foram incluídos onde os formulários os esperam; a tabela de esforço não os inclui porque a interface percorre os seus campos como dias da semana.

## Validação

O esquema foi instalado num MySQL isolado e preenchido com dados fictícios. Foram testados login e páginas de instituição, curso, turma, listagens de alunos, professores e disciplinas, e leitura do calendário pessoal. Isso confirma a compatibilidade dos caminhos exercitados; não valida todos os controladores de edição, convites e progressão de ano letivo.
