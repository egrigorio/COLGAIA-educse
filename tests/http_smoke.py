"""Smoke HTTP sem banco ou SMTP. Executa e encerra um servidor isolado."""
import http.cookiejar
import json
from html.parser import HTMLParser
import os
from pathlib import Path
import re
import socket
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

root = Path(__file__).resolve().parents[1]
with socket.socket() as sock:
    sock.bind(('127.0.0.1', 0))
    port = sock.getsockname()[1]
base = f'http://127.0.0.1:{port}'
env = dict(os.environ, APP_URL=base, APP_DEBUG='true')
jar = http.cookiejar.CookieJar()
class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *args, **kwargs):
        return None
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar), NoRedirect)
def request(path, data=None, headers=None):
    payload = None if data is None else urllib.parse.urlencode(data).encode()
    try:
        response = client.open(urllib.request.Request(base + path, payload, headers={'Referer': base + '/admin/', **(headers or {})}))
    except urllib.error.HTTPError as error:
        response = error
    body = response.read().decode()
    assert not re.search(r'(?:Warning:|Fatal error:|Deprecated:)', body), body
    return response.status, response.headers, body
with tempfile.TemporaryFile() as log:
    server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', 'scripts/router.php'], cwd=root, env=env, stdout=log, stderr=log)
    try:
        for _ in range(100):
            try:
                with socket.create_connection(('127.0.0.1', port), timeout=.1):
                    break
            except OSError:
                time.sleep(.05)
        count = 0
        for page in ['login.php', 'registo.php', 'email_recuperar_senha.php', 'recuperar_senha.php']:
            status, headers, body = request('/pages/auth/' + page)
            assert status == 200, (page, status)
            assert 'name="csrf_token"' in body, page
            assert 'lang="pt"' in body
            count += 1
        token = re.search('name="csrf_token" value="([a-f0-9]+)"', body).group(1)
        status, headers, _ = request('/')
        assert status == 302 and headers['Location'] == base + '/admin/'
        status, headers, _ = request('/admin/')
        assert status == 302 and 'login.php' in headers['Location']
        for handler in ['login', 'login_convite', 'registo', 'registo_convite', 'verifconta', 'recuperar_senha']:
            path = f'/modules/trata_{handler}.mod.php'
            assert request(path)[0] == 405, handler
            assert request(path, {})[0] == 419, handler
            count += 2
        public_handlers = {'trata_convite_user_curso.mod.php', 'trata_convite_user_plataforma_curso.mod.php', 'trata_convite_instituicao.mod.php'}
        for module in (root / 'modules').glob('*.php'):
            if module.name in public_handlers:
                continue
            path = '/modules/' + module.name
            assert request(path)[0] == 405, module.name
            assert request(path, {})[0] == 419, module.name
            count += 2
        assert request('/modules/trata_remover_curso_instituicao.mod.php', {'csrf_token': token, 'id_curso': 1})[0] == 401
        status, headers, _ = request('/modules/trata_login.mod.php', {'csrf_token': token})
        assert status == 302 and headers['Location'].endswith('/login.php')
        for path in ['/.env', '/composer.json', '/include/config.inc.php', '/vendor/autoload.php', '/app/http.php', '/.git/config', '/tests/run.php']:
            assert request(path)[0] == 404, path
            count += 1
        assert request('/public/styles.css')[0] == 200
        assert request('/modules/trata_logout.mod.php')[0] == 405
        assert request('/modules/trata_logout.mod.php', {'csrf_token': token})[0] == 302
        demo_password = os.environ.get('EDUCSE_HTTP_DEMO_PASSWORD')
        if demo_password:
            invitation = json.loads(subprocess.check_output(['php', 'tests/invitation_fixture.php'], cwd=root, env=env))
            for role, paths in {
                'instituicao': ['/admin/', '/admin/instituicao.php'],
                'professor': ['/admin/', '/admin/curso.php', '/admin/turma.php?id_turma=1', '/admin/dashboards/gerar_tabelas_views_professores.php?valor=efetivos', '/admin/dashboards/gerar_tabelas_views_alunos.php?valor=efetivos', '/admin/dashboards/gerar_tabelas_views_disciplinas.php?valor=efetivos'],
                'aluno': ['/admin/', '/admin/turma.php?id_turma=1', '/admin/dashboards/get_eventos_aluno.php'],
            }.items():
                _, _, body = request('/pages/auth/login.php')
                token = re.search('name="csrf_token" value="([a-f0-9]+)"', body).group(1)
                status, headers, _ = request('/modules/trata_login.mod.php', {'csrf_token': token, 'user_or_mail': 'demo_' + role, 'password': demo_password})
                assert status == 302 and '/admin/' in headers['Location'], role
                for path in paths:
                    status, _, body = request(path)
                    assert status == 200, (role, path, status, body[:400])
                    for script in re.findall(r'<script\b[^>]*>([\s\S]*?)</script>', body, re.I):
                        if not script.strip():
                            continue
                        parsed = subprocess.run(['node', '--check'], input=script, text=True, capture_output=True)
                        assert parsed.returncode == 0, (path, parsed.stderr)
                    for form in re.findall(r'<form\b[^>]*>[\s\S]*?</form>', body, re.I):
                        if re.search(r'\bmethod=["\']post["\']', form, re.I):
                            assert 'name="csrf_token"' in form, path
                    count += 1
                _, _, page = request('/admin/')
                token = re.search('name="csrf-token" content="([a-f0-9]+)"', page).group(1)
                def mutation(name, values):
                    return request('/modules/trata_' + name + '.mod.php', {'csrf_token': token, **values}, {'Accept': 'application/json'})
                if role == 'aluno':
                    assert mutation('remover_curso_instituicao', {'id_curso': 1})[0] == 403
                    assert mutation('excluir_user_curso', {'id_curso': 1, 'id_user': 2})[0] == 403
                    assert mutation('esforco_turma', {'id_turma': 1, 'id_esforco': 1, 'limite': 4, 'barreira': 3})[0] == 403
                    assert mutation('editar_instituicao', {'tipo': 'editar', 'id_instituicao': 1, 'nome': 'Outra escola'})[0] == 403
                    assert mutation('inserir', {'modulo': "users; DROP TABLE users"})[0] == 422
                    assert request('/admin/dashboards/gerar_tabelas_views_professores.php?valor=efetivos')[0] == 403
                    assert request('/admin/turma.php?id_turma=1%20OR%201=1')[0] == 422
                    if not env.get('LCSOPAI'):
                        assert request('/admin/dashboards/chamar_api_openai.php', {'csrf_token': token, 'id_turma': 1, 'message': 'Quais são as atividades?'})[0] == 503
                    event = {'start': '2030-01-07T10:00:00', 'end': '2030-01-07T11:00:00', 'title': "Estudo d'PHP", 'description': 'Praticar', 'backgroundColor': '#3788d8'}
                    assert mutation('adicionar_eventos_ai', {'eventos': json.dumps([event])})[0] == 200
                    assert mutation('adicionar_eventos_ai', {'eventos': 'not json'})[0] == 422
                    events = json.loads(request('/admin/dashboards/get_eventos_aluno.php')[2])
                    assert any(row['title'] == event['title'] for row in events)
                    assert request('/admin/dashboards/apagar_eventos_aluno.php')[0] == 405
                    assert request('/admin/dashboards/apagar_eventos_aluno.php', {'csrf_token': token}, {'Accept': 'application/json'})[0] == 200
                    assert json.loads(request('/admin/dashboards/get_eventos_aluno.php')[2]) == []
                    invitation_path = '/modules/trata_convite_user_curso.mod.php'
                    status, _, confirmation = request(invitation_path + '?token=' + invitation['token'])
                    assert status == 200 and '<form method="post">' in confirmation
                    assert request(invitation_path, {'csrf_token': token, 'token': invitation['token']})[0] == 302
                    assert request(invitation_path + '?token=' + invitation['token'])[0] == 404
                    count += 15
                elif role == 'professor':
                    assert mutation('editar_instituicao', {'tipo': 'editar', 'id_instituicao': 1, 'nome': 'Outra escola'})[0] == 403
                    assert mutation('mudar_tema', {'theme': 'nocas'})[0] == 204
                    assert mutation('mudar_tema', {'theme': '<script>'})[0] == 422
                    assert mutation('editar_atividade_turma', {'id_turma': 1, 'id_evento': 1, 'titulo': "Exercício d'PHP", 'descricao': 'Descrição alterada', 'comeco': '2030-01-07', 'fim': '2030-01-11', 'tipo': 'Trabalho', 'disciplina': 1, 'tempo_sugerido': 3, 'turno': -1})[0] == 200
                    assert mutation('editar_disciplina_curso', {'id_curso': 1, '1%1': 'on'})[0] == 200
                    assert mutation('editar_turma_diretor_curso', {'id_curso': 1, 'diretor_turma_1': 2})[0] == 200
                    assert mutation('editar_turno_user', {'id_turma': 1, 'cargo': 'alunos', 'turno_3': -1})[0] == 200
                    assert mutation('inserir_aluno_turma', {'id_curso': 1, 'select_id_turma%3': 1})[0] == 200
                    assert mutation('editar_user_curso', {'id_curso': 1, 'id_user': 2, 'Disciplina[0]': 1, 'Turma[0]': 1})[0] == 200
                    count += 9
                elif role == 'instituicao':
                    assert mutation('editar_instituicao', {'tipo': 'editar', 'id_instituicao': 1, 'nome': "Escola d'Aplicações"})[0] == 200
                    assert request('/modules/trata_convite_user_curso.mod.php?token=' + invitation['token'])[0] == 403
                    result = mutation('editar_instituicao', {'tipo': 'editar', 'id_instituicao': 999999, 'nome': 'Outra escola'})
                    assert result[0] == 403, result
                    count += 2
                token = re.search('name="csrf-token" content="([a-f0-9]+)"', request('/admin/')[2]).group(1)
                assert request('/modules/trata_logout.mod.php', {'csrf_token': token})[0] == 302
        print(f'{count + 5} verificações HTTP passaram.')
    finally:
        server.terminate()
        server.wait(timeout=5)
