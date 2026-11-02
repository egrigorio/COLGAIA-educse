"""Smoke HTTP sem banco ou SMTP. Executa e encerra um servidor isolado."""
import http.cookiejar
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
def request(path, data=None):
    payload = None if data is None else urllib.parse.urlencode(data).encode()
    try:
        response = client.open(urllib.request.Request(base + path, payload, headers={'Referer': base + '/admin/'}))
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
        status, headers, _ = request('/modules/trata_login.mod.php', {'csrf_token': token})
        assert status == 302 and headers['Location'].endswith('/login.php')
        for path in ['/.env', '/composer.json', '/include/config.inc.php', '/vendor/autoload.php', '/app/http.php', '/.git/config', '/tests/run.php']:
            assert request(path)[0] == 404, path
            count += 1
        assert request('/public/styles.css')[0] == 200
        assert request('/modules/trata_logout.mod.php')[0] == 302
        demo_password = os.environ.get('EDUCSE_HTTP_DEMO_PASSWORD')
        if demo_password:
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
                    count += 1
                request('/modules/trata_logout.mod.php')
        print(f'{count + 5} verificações HTTP passaram.')
    finally:
        server.terminate()
        server.wait(timeout=5)
