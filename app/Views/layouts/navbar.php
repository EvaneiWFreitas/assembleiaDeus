<nav class="navbar navbar-expand navbar-light app-navbar px-3">
    <button class="btn btn-link text-dark p-0 me-3" id="btnSidebar" type="button" title="Menu">
        <i class="fa-solid fa-bars fa-lg"></i>
    </button>
    <a class="navbar-brand fw-bold" href="<?= site_url('dashboard') ?>">
        <i class="fa-solid fa-church text-primary me-1"></i> Gestão de Igrejas
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2">
                    <?= esc(mb_substr($usuario['nome'] ?? 'U', 0, 1)) ?>
                </span>
                <span class="d-none d-md-inline small">
                    <strong><?= esc($usuario['nome'] ?? 'Usuário') ?></strong><br>
                    <span class="text-muted"><?= esc($usuario['role'] ?? '') ?></span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="<?= site_url('senha') ?>"><i class="fa-solid fa-key me-2"></i>Alterar senha</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Sair</a></li>
            </ul>
        </div>
    </div>
</nav>
