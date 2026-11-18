document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('adminSidebar');
    const links = document.querySelectorAll('.admin-sidebar .admin-menu-link');

    if (window.innerWidth < 992 && sidebar) {
        links.forEach(function (link) {
            link.addEventListener('click', function () {
                const instance = window.bootstrap && window.bootstrap.Collapse
                    ? window.bootstrap.Collapse.getInstance(sidebar)
                    : null;
                if (instance) {
                    instance.hide();
                }
            });
        });
    }

    document.querySelectorAll('.alert-dismissible .btn-close').forEach(function (button) {
        button.addEventListener('click', function () {
            const alert = button.closest('.alert');
            if (alert) {
                alert.classList.remove('show');
            }
        });
    });
});
