# Reestruturação do educse

## Base da aplicação

O bootstrap carrega ambiente, autoload e sessão. A configuração deixou de depender de XAMPP e a conexão MySQL é aberta apenas quando necessária. `include/config.inc.php` mantém compatibilidade com as vistas antigas; os controladores usam `bootstrap/management.php`, sem carregar HTML nem executar consultas em função de parâmetros GET.

Dependências geradas e `.DS_Store` deixaram de ser versionados. Os lockfiles foram mantidos. O MySQL da CI recebe uma password aleatória por execução, mascarada nos logs; o contentor é removido no final.

As credenciais SMTP foram retiradas do código. As credenciais anteriormente publicadas devem ser revogadas no fornecedor: removê-las desta versão não as elimina do histórico. Configure substituições apenas no `.env`.

## Controladores de gestão

Os ficheiros `modules/trata_*.mod.php` continuam a ser as entradas HTTP para evitar quebrar os endereços dos formulários. Os 25 controladores de gestão delegam no `ManagementController`; já não montam SQL nem contêm regras escolares.

O fluxo é: **requisição → método e CSRF → validação → autorização por recurso → serviço → repositório → resposta**.

| Componente | Responsabilidade |
| --- | --- |
| `Input` | Tipos, limites, listas, datas e leitura dos nomes de campos legados |
| `SchoolPolicy` | Conta ativa e permissão sobre a instituição, curso, turma ou inscrição consultada no banco |
| `CourseService` | Instituição, cursos, criação de turmas, progressão e exclusão completa de cursos |
| `ActivityService` | Criação/edição/exclusão de atividades, esforço e eventos pessoais |
| `SubjectService` | Disciplinas da instituição, disciplinas do curso e plano curricular |
| `MembershipService` | Inscrições, diretores de turma, professores, alunos e turnos |
| `InvitationService` | Emissão, destinatário, expiração, cancelamento e consumo de convites |
| `WorkloadService` | Contagem inclusiva dos dias úteis e cálculo sem divisão por zero |
| `SchoolRepository` | Consultas parametrizadas, identificadores permitidos, operações em conjunto e transações |

O repositório só aceita tabelas e colunas de uma lista interna. A antiga inserção genérica aceita apenas a criação de turma, com campos explícitos. O endpoint vazio de convite de instituição responde 404; o antigo endpoint vazio de remoção de utilizador delega na remoção segura da inscrição.

## Regras implementadas

- Uma instituição gere apenas as suas próprias instituições e cursos. Um diretor de curso gere apenas o curso que dirige. O cargo é lido da conta ativa no banco, não aceite de um formulário.
- A direção de turma ou de curso pode gerir esforço e turnos da turma. Um professor só cria/edita atividades das disciplinas que leciona numa turma a que tem acesso; o diretor do curso também pode fazê-lo. A edição e remoção individual de uma atividade exigem autoria.
- Alunos só escrevem no seu próprio calendário; o id do utilizador é retirado da sessão. Eventos gerados são validados novamente no servidor, independentemente de virem da IA ou do navegador.
- As ações de escrita exigem POST e CSRF, incluindo exclusões, logout, tema e calendário. Os formulários renderizados recebem um token; o transporte AJAX utiliza o mesmo token em cabeçalho. GET não aceita convites nem apaga dados.
- Exclusões de curso removem inscrições, turnos associados, turmas, esforço não partilhado, relações e atividades/eventos que ficam sem vínculo. Contas e disciplinas globais são preservadas.
- Uma disciplina partilhada não é apagada globalmente pela instituição. Se ainda estiver em uso nos seus cursos, a remoção é recusada até que esses vínculos sejam removidos.
- A edição de um professor mantém os turnos das inscrições que não mudaram. Mover um aluno não apaga inscrições de outros cursos ou anos anteriores.
- A progressão cria todos os anos com o índice correto, associa as disciplinas da turma certa, move apenas alunos, preserva inscrições antigas como inativas e recusa um ano de destino já criado. O curso é bloqueado durante a operação para serializar repetições concorrentes.
- Operações com várias alterações são transacionais. As inscrições/turnos são removidos em conjunto em vez de fazer uma consulta por aluno. O cálculo de dias úteis percorre no máximo seis dias residuais por intervalo.
- Os erros de validação/autorização têm estados HTTP próprios; erros internos não devolvem SQL nem stack traces ao cliente. Respostas AJAX usam JSON e formulários bem-sucedidos redirecionam.

## Convites e atualização do banco

Execute `composer db:migrate` numa instalação da etapa anterior antes de utilizar os novos convites. A migração é repetível e acrescenta `token_hash` e `expira_em` sem apagar dados.

Cada convite novo usa 32 bytes aleatórios, guarda apenas SHA-256 no banco, dura sete dias, pertence a um email/cargo e é consumido uma vez. Os links antigos com apenas um id numérico deixam de ser aceites: os convites pendentes precisam de ser reenviados.

O link prepara o cadastro/login ou apresenta confirmação para uma conta já autenticada. O aceite ocorre apenas por POST protegido ou como parte do envio protegido do cadastro/login. Cadastro por convite e inscrição são confirmados na mesma transação.

O email é enviado após guardar o convite. Se o SMTP falhar, a resposta informa que o convite ficou guardado e precisa de novo envio. Um lote pode ter entregas parciais: não se pode desfazer um email já enviado. Os testes utilizam um transportador falso e não enviam emails.

## Verificação

A suíte cobre autenticação, autorização entre instituições/cargos, preservação de outros cursos, entradas SQL como dados, alteração de atividades, progressão repetida, rollback, destinatário/expiração/reutilização de convites, dias úteis e ausência de divisão por zero.

O teste HTTP usa dados demonstrativos num banco descartável e verifica métodos, CSRF, formulários, respostas de gestão, calendário, confirmação de convite e sintaxe do JavaScript renderizado. A interface também foi inspecionada num navegador local, incluindo os formulários compostos e a ausência de erros no console.

## Trabalho ainda separado desta refatoração

- As vistas extensas ainda contêm HTML, JavaScript e consultas de leitura misturados. Os campos de apresentação mais usados e os títulos do calendário receberam escape; uma revisão completa de todas as vistas continua recomendada antes de publicação.
- Ainda não há foreign keys. É necessário auditar os dados existentes, eliminar duplicações antigas e definir as políticas de retenção antes de acrescentá-las. A camada de serviços protege os fluxos refatorados; não corrige retroativamente dados inconsistentes.
- Recuperação de senha e verificação normal de cadastro ainda dependem da sessão do navegador. Persistência própria e limitação de tentativas/envios continuam pendentes.
- Não foi configurado envio real por SMTP nem testada uma chamada paga ao assistente. A IA passou a exigir sessão e pertença à turma, sem credencial partilhada no cliente. O modelo legado foi mantido como padrão e pode ser configurado por `OPENAI_MODEL`.
- Agrupamentos semanais e estatísticas que atravessam meses/anos ainda precisam de revisão funcional própria.
- A cadeia de CSS ainda tem três alertas moderados de desenvolvimento em `glob`, `minimatch` e `sucrase`. Não foram forçadas versões principais incompatíveis com Tailwind.

Esta mudança mantém PHP e as páginas originais. Não é uma troca de framework nem um redesenho visual.
