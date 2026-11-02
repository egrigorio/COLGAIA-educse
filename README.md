# educse

Plataforma de gestão de esforço estudantil e atividades escolares, desenvolvida como PAP (Prova de Aptidão Profissional). Inclui instituições, cursos, turmas, disciplinas, atividades, calendário e um assistente de IA.

O projeto está em reorganização gradual, preservando PHP e as páginas originais. A autenticação já possui serviços e repositório separados; as telas de gestão ainda contêm código legado. O banco foi reconstruído por engenharia reversa: permite iniciar uma instalação nova, mas não recupera os dados antigos.

## Executar localmente

Requisitos: PHP 8.2 ou superior com `mysqli`, Composer, MySQL 5.7+/8.x ou MariaDB compatível, Node.js e npm. A validação local foi feita com PHP 8.4 e MySQL 5.7.

```sh
composer install
npm ci
cp .env.example .env
```

Preencha os campos `DB_*` no `.env` com os dados de um banco local vazio. O instalador cria o banco se o utilizador tiver permissão e recusa executá-lo quando já existem tabelas.

```sh
composer db:install
npm run build
composer serve
```

Abra [educse local](http://127.0.0.1:8000). Para recompilar o CSS durante alterações, use `npm run dev` noutro terminal. O servidor de desenvolvimento bloqueia acesso HTTP a configurações, dependências e ficheiros internos. Não deve ser usado em produção.

## Demonstração sem configurar email

Num banco recém-instalado e com `APP_ENV=local`:

```sh
composer db:seed-demo
```

O comando cria uma instituição, um curso, uma turma, uma disciplina e uma atividade. Apresenta uma password aleatória comum para `demo_instituicao`, `demo_professor` e `demo_aluno`. Recusa executar quando já existem utilizadores; não altera contas existentes.

## Configuração

- `APP_URL`: opcional para navegação local; para emails, use a URL absoluta, por exemplo `http://127.0.0.1:8000`. Em subpastas, inclua o prefixo, como `https://exemplo.pt/educse`.
- `APP_DEBUG`: `true` em desenvolvimento e `false` fora dele.
- `APP_TIMEZONE`: por padrão, `Europe/Lisbon`.
- `DB_*`: ligação MySQL; a conexão só é aberta quando uma consulta é executada.
- `SMTP_*` e `MAIL_FROM_*`: necessários para cadastro, recuperação de senha e convites por email. Uma falha de envio já não é apresentada como sucesso no cadastro normal.
- `LCSOPAI` e `CHAVE_ENVIAR`: nomes legados da configuração do assistente. A integração de IA ainda precisa de revisão própria; não é necessária para abrir as páginas de autenticação.

A verificação do cadastro expira em 15 minutos e aceita até cinco tentativas. A recuperação de senha expira em 30 minutos e o token é consumido após uso. Por compatibilidade, ambos ainda dependem da sessão do navegador que iniciou o processo; links de recuperação devem ser abertos nesse mesmo navegador.

## Organização

```text
app/Repositories/   Consultas de utilizadores com parâmetros SQL
app/Services/       Autenticação e validação do cadastro
app/http.php        Redirecionamentos, leitura de formulários e CSRF
bootstrap/         Autoload, ambiente e sessão
config/            Configuração central
include/           Compatibilidade e funções legadas
admin/             Páginas e componentes de gestão existentes
modules/           Entradas HTTP para ações dos formulários
pages/auth/        Páginas de autenticação
database/          Esquema SQL reconstruído
scripts/           Servidor local, instalação, demonstração e lint
public/            Imagens, fontes e CSS compilado
tests/             Testes PHP e HTTP
```

`vendor/` e `node_modules/` são instalados pelos respetivos gestores e não devem ser versionados. Os lockfiles mantêm as versões reproduzíveis.

## Verificar alterações

```sh
composer validate
composer lint
composer test
python3 tests/http_smoke.py
npm run build
composer audit
npm audit
```

Para executar também os testes de integração com MySQL, configure `DB_*` e execute:

```sh
EDUCSE_TEST_DATABASE=1 composer test
```

Os testes PHP usam uma tabela `users` temporária, exclusiva de cada conexão. Para verificar HTTP com os dados demonstrativos, depois do seed:

```sh
EDUCSE_HTTP_DEMO_PASSWORD='password apresentada pelo seed' python3 tests/http_smoke.py
```

O teste HTTP inicia e encerra o seu próprio servidor. O teste demonstrativo assume a turma de id 1 de uma instalação nova. A integração contínua repete lint, autenticação, instalação do esquema e testes HTTP num MySQL descartável.

Consulte [as decisões do banco](docs/banco-de-dados.md) e [o diagnóstico e próximas etapas](docs/reestruturacao.md).
