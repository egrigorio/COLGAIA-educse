/* Transporte dos formulários legados; autorização e validação permanecem no servidor. */
function educsePost(address, values = {}) {
    const url = new URL(address, window.location.href);
    if (url.origin !== window.location.origin) throw new Error('Destino inválido');
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url.pathname;
    const fields = new Map(url.searchParams.entries());
    for (const [key, value] of Object.entries(values)) fields.set(key, value);
    fields.set('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    for (const [name, value] of fields) {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = name; input.value = value;
        form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
}

function educseSetupAjax($) {
    $(document).ajaxError(function (_event, xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.error;
        if (message && window.Swal) Swal.fire({icon: 'error', text: message});
    });
    $.ajaxPrefilter(function (options, original, xhr) {
        const url = new URL(options.url, window.location.href);
        if (url.origin === window.location.origin && !['GET', 'HEAD'].includes(options.type.toUpperCase())) {
            xhr.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]').content);
        }
    });
}

document.addEventListener('click', function (event) {
    const link = event.target.closest('a[data-post]');
    if (!link) return;
    event.preventDefault();
    educsePost(link.href);
});

function educseEscape(value) {
    const element = document.createElement('span');
    element.textContent = value == null ? '' : String(value);
    return element.innerHTML;
}
