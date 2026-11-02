# Reestruturação do educse

## Alterações implementadas

A configuração deixou de depender de um caminho XAMPP e de `HTTP_HOST` para gerar URLs. O `.env` é carregado uma vez, com suporte a variáveis do processo. O bootstrap prepara sessão e autoload; as configurações ficam em `config/` e o ficheiro antigo continua como ponto de compatibilidade.

O acesso aos utilizadores está em `UserRepository`; a validação de cadastro e login, em `AuthService`. Login normal e por convite partilham essa lógica. As consultas de conta usam parâmetros SQL, login renova o id da sessão e os formulários de autenticação verificam CSRF e método POST. Códigos e tokens têm expiração e são limpos após uso.

O MySQL descartável da CI recebe uma password aleatória a cada execução, mascarada nos logs e disponibilizada apenas aos passos desse job. O contentor é removido no final. Não há password de CI fixa no workflow.

As credenciais SMTP foram retiradas do código. As credenciais anteriormente publicadas devem ser revogadas no fornecedor: a remoção desta versão não as elimina do histórico Git. Não é necessário partilhar novas credenciais no chat; configure-as apenas no `.env`.

Dependências geradas e ficheiros `.DS_Store` deixaram de ser versionados. Mantêm-se os lockfiles. As bibliotecas PHP foram atualizadas dentro das versões permitidas; a dependência JWT não utilizada foi removida. O build Tailwind procura classes apenas no código da aplicação e existe um comando de build finito.

A base MySQL foi reconstruída com instalador que recusa bancos preenchidos e seed de demonstração com password aleatória. As decisões e incertezas estão em `banco-de-dados.md`.

## Pendências identificadas

1. Migrar gradualmente turmas, cursos, disciplinas e atividades para serviços e repositórios. Ainda existem queries interpoladas e validações inconsistentes nesses módulos.
2. Rever autorização por recurso em todos os endpoints, incluindo AJAX e IA. A autenticação foi melhorada, mas a aplicação completa ainda não está preparada para exposição pública.
3. Aplicar CSRF e métodos adequados às ações de gestão e logout; várias operações legadas continuam por GET.
4. Rever convites: ids sequenciais, expiração, destinatário, transações e vínculo à instituição. A refatoração preservou o fluxo existente, sem certificar a segurança desses convites.
5. Guardar verificação e recuperação em tabelas próprias, para funcionarem entre navegadores; acrescentar limitação de tentativas de login e envio de email.
6. Corrigir os cálculos de esforço para períodos sem dias úteis, agrupamentos semanais e intervalos que atravessam meses/anos. Há risco de divisão por zero no código legado.
7. Separar consultas, HTML e JavaScript dos ficheiros extensos `layout.dash.php` e `functions.inc.php`; uniformizar escape de saída e mensagens.
8. Acrescentar foreign keys e transações após eliminar sentinelas e ordenar corretamente as exclusões. Revisar duplicação de inscrições e progressão de anos letivos.
9. Rever a integração de IA: autorização por turma, credencial partilhada exposta no cliente, modelo configurável, resposta estruturada e validação dos eventos gerados.
10. Atualizar a cadeia de ferramentas de CSS. Após atualizar as versões compatíveis, `npm audit` ainda reporta três alertas moderados em `glob`, `minimatch` e `sucrase`, usados em desenvolvimento. A próxima atualização deve validar compatibilidade com Tailwind antes de forçar novas versões principais.

Não foi feita uma migração de framework nem um redesenho visual. A prioridade desta etapa foi tornar o projeto instalável, reduzir duplicação na autenticação e permitir testes antes de alterações maiores.
