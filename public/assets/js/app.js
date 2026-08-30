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
});
