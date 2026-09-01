/* JS global do sistema */
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar (desktop: recolhe; mobile: overlay)
    document.getElementById('btnSidebar')?.addEventListener('click', function () {
        if (window.innerWidth < 992) {
            document.body.classList.toggle('sidebar-aberta');
        } else {
            document.body.classList.toggle('sidebar-recolhida');
        }
    });

    // Confirmação antes de excluir
    document.querySelectorAll('[data-confirmar]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirmar') || 'Confirma esta operação?')) {
                e.preventDefault();
            }
        });
    });

    // Máscaras de formulário
    if (window.jQuery && jQuery.fn.mask) {
        jQuery('.mascara-cpf').mask('000.000.000-00');
        jQuery('.mascara-cnpj').mask('00.000.000/0000-00');
        jQuery('.mascara-cep').mask('00000-000');
        jQuery('.mascara-telefone').mask('(00) 00000-0000');
    }

    // DataTables padrão (tabelas com .tabela-dados)
    if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('.tabela-dados').each(function () {
            jQuery(this).DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json'
                },
                pageLength: 25
            });
        });
    }

    // Favicon dinâmico: usa o ícone do item ativo do menu
    atualizarFavicon();
});

function atualizarFavicon() {
    // Se já houver favicon estático apontando para a logo da igreja, não sobrescrever
    if (document.querySelector('link[rel="icon"][href*="/uploads/"], link[rel="shortcut icon"][href*="/uploads/"]')) return;

    const icone = document.querySelector('.app-sidebar a.nav-link.active i.fa-solid');
    if (!icone) return;

    const conteudo = getComputedStyle(icone, '::before').content;
    // O navegador devolve o glifo real (ex: "\uf015") entre aspas
    let glifo = (conteudo || '').replace(/^(['"])|(['"])$/g, '');
    if (!glifo || glifo === 'none' || glifo === 'normal') return;

    const fonte = '900 42px "Font Awesome 6 Free", sans-serif';
    const canvas = document.createElement('canvas');
    canvas.width = 64;
    canvas.height = 64;
    const ctx = canvas.getContext('2d');

    const desenhar = () => {
        ctx.font = fonte;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = '#2563eb';
        ctx.fillRect(0, 0, 64, 64);
        ctx.fillStyle = '#ffffff';
        ctx.fillText(glifo, 32, 33);

        let favicon = document.querySelector('link[rel="icon"]');
        if (!favicon) {
            favicon = document.createElement('link');
            favicon.rel = 'icon';
            document.head.appendChild(favicon);
        }
        favicon.type = 'image/png';
        favicon.href = canvas.toDataURL('image/png');
    };

    // Espera a fonte Font Awesome carregar para renderizar o glifo corretamente
    document.fonts.load(fonte).then(desenhar).catch(desenhar);
}
