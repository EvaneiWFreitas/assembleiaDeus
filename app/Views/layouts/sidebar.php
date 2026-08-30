<?php
/** Sidebar — itens ocultos caso o usuário não tenha a permissão base do módulo. */
$tem = static fn (string $m): bool => ($permissoes === '*' || in_array($m . ':visualizar', $permissoes, true) || in_array($m . ':*', $permissoes, true) || in_array('*:visualizar', $permissoes, true));
$atual = strtolower(service('uri')->getSegment(1) ?: 'dashboard');
?>
<aside class="app-sidebar" id="appSidebar">
    <ul class="nav flex-column p-2">
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>

        <?php if ($tem('usuarios') || $tem('roles')): ?>
        <li class="nav-item"><span class="nav-label">Administração</span></li>
        <?php if ($tem('usuarios')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'usuarios' ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">
                <i class="fa-solid fa-user-gear"></i> Usuários
            </a>
        </li>
        <?php endif; ?>
        <?php if ($tem('roles')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'roles' ? 'active' : '' ?>" href="<?= site_url('roles') ?>">
                <i class="fa-solid fa-shield-halved"></i> Perfis & Permissões
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <li class="nav-item"><span class="nav-label">Igreja</span></li>
        <?php if (tem_permissao('igreja', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'igreja' ? 'active' : '' ?>" href="<?= site_url('igreja') ?>">
                <i class="fa-solid fa-church"></i> Dados da Igreja
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('congregacoes', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'congregacoes' ? 'active' : '' ?>" href="<?= site_url('congregacoes') ?>">
                <i class="fa-solid fa-place-of-worship"></i> Congregações
            </a>
        </li>
        <?php endif; ?>

        <?php if (tem_permissao('membros', 'visualizar') || tem_permissao('visitantes', 'visualizar') || tem_permissao('obreiros', 'visualizar')): ?>
        <li class="nav-item"><span class="nav-label">Pessoas</span></li>
        <?php if (tem_permissao('membros', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'membros' ? 'active' : '' ?>" href="<?= site_url('membros') ?>">
                <i class="fa-solid fa-people-group"></i> Membros
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('visitantes', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'visitantes' ? 'active' : '' ?>" href="<?= site_url('visitantes') ?>">
                <i class="fa-solid fa-user-plus"></i> Visitantes
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('obreiros', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'obreiros' ? 'active' : '' ?>" href="<?= site_url('obreiros') ?>">
                <i class="fa-solid fa-user-tie"></i> Pastores & Obreiros
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link disabled" href="#" title="Disponível na Etapa 4"><i class="fa-solid fa-calendar-days"></i> Agenda <span class="badge bg-secondary ms-auto">E4</span></a></li>
        <li class="nav-item"><a class="nav-link disabled" href="#" title="Disponível na Etapa 6"><i class="fa-solid fa-coins"></i> Financeiro <span class="badge bg-secondary ms-auto">E6</span></a></li>
        <li class="nav-item"><a class="nav-link disabled" href="#" title="Disponível na Etapa 8"><i class="fa-solid fa-chart-bar"></i> Relatórios <span class="badge bg-secondary ms-auto">E8</span></a></li>
    </ul>
</aside>
