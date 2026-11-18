/* ============================================================
   assembleiaDeus - JavaScript do site institucional
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    var links = document.querySelectorAll('a[href^="#"]');
    links.forEach(function (link) {
        link.addEventListener('click', function (event) {
            var alvo = document.querySelector(this.getAttribute('href'));
            if (alvo) {
                event.preventDefault();
                alvo.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    var formulario = document.querySelector('.ad-form-contato');
    if (formulario) {
        formulario.addEventListener('submit', function (event) {
            if (!formulario.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            formulario.classList.add('was-validated');
        }, false);
    }

    var mensagens = document.querySelectorAll('.alert-dismissible');
    mensagens.forEach(function (mensagem) {
        window.setTimeout(function () {
            var instancia = bootstrap.Alert.getOrCreateInstance(mensagem);
            instancia.close();
        }, 7000);
    });
});
