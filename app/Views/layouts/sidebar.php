<?php
/** Sidebar — itens ocultos caso o usuário não tenha a permissão base do módulo. */
$atual = strtolower(service('uri')->getSegment(1) ?: 'dashboard');
?>
<aside class="app-sidebar" id="appSidebar">
    <ul class="nav flex-column p-2">
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= site_url('/') ?>" target="_blank" rel="noopener" title="Abrir a página principal do site em nova aba">
                <i class="fa-solid fa-globe"></i> Página Principal
            </a>
        </li>

        <?php if (tem_permissao('usuarios', 'visualizar') || tem_permissao('roles', 'visualizar')): ?>
        <li class="nav-item"><span class="nav-label">Administração</span></li>
        <?php if (tem_permissao('usuarios', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'usuarios' ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">
                <i class="fa-solid fa-user-gear"></i> Usuários
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('roles', 'visualizar')): ?>
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
        <?php if (tem_permissao('banners', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'banners' ? 'active' : '' ?>" href="<?= site_url('banners') ?>">
                <i class="fa-solid fa-images"></i> Banners (Site)
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

        <?php if (tem_permissao('membros', 'visualizar') || tem_permissao('visitantes', 'visualizar') || tem_permissao('obreiros', 'visualizar') || tem_permissao('carteirinhas', 'visualizar')): ?>
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
        <?php if (tem_permissao('carteirinhas', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'carteirinhas' ? 'active' : '' ?>" href="<?= site_url('carteirinhas') ?>">
                <i class="fa-solid fa-id-card"></i> Carteirinhas
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (tem_permissao('departamentos', 'visualizar') || tem_permissao('ministerios', 'visualizar') || tem_permissao('celulas', 'visualizar') || tem_permissao('discipulados', 'visualizar') || tem_permissao('cursos', 'visualizar') || tem_permissao('cargos', 'visualizar')): ?>
        <li class="nav-item"><span class="nav-label">Ministério</span></li>
        <?php if (tem_permissao('cargos', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'cargos' ? 'active' : '' ?>" href="<?= site_url('cargos') ?>">
                <i class="fa-solid fa-briefcase"></i> Cargos de Obreiros
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('departamentos', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'departamentos' ? 'active' : '' ?>" href="<?= site_url('departamentos') ?>">
                <i class="fa-solid fa-sitemap"></i> Departamentos
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('ministerios', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'ministerios' ? 'active' : '' ?>" href="<?= site_url('ministerios') ?>">
                <i class="fa-solid fa-hands-praying"></i> Ministérios
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('celulas', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'celulas' ? 'active' : '' ?>" href="<?= site_url('celulas') ?>">
                <i class="fa-solid fa-house-chimney-user"></i> Células
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('discipulados', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'discipulados' ? 'active' : '' ?>" href="<?= site_url('discipulados') ?>">
                <i class="fa-solid fa-hands-holding-circle"></i> Discipulado
            </a>
        </li>
        <?php endif; ?>
        <?php if (tem_permissao('cursos', 'visualizar')): ?>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'cursos' ? 'active' : '' ?>" href="<?= site_url('cursos') ?>">
                <i class="fa-solid fa-book-bible"></i> Cursos
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (tem_permissao('financeiro', 'visualizar')): ?>
        <li class="nav-item"><span class="nav-label">Financeiro</span></li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'financeiro' ? 'active' : '' ?>" href="<?= site_url('financeiro') ?>">
                <i class="fa-solid fa-coins"></i> Painel
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'dizimos' ? 'active' : '' ?>" href="<?= site_url('dizimos') ?>">
                <i class="fa-solid fa-hand-holding-dollar"></i> Dízimos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'ofertas' ? 'active' : '' ?>" href="<?= site_url('ofertas') ?>">
                <i class="fa-solid fa-box-open"></i> Ofertas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'despesas' ? 'active' : '' ?>" href="<?= site_url('despesas') ?>">
                <i class="fa-solid fa-money-bill-wave"></i> Despesas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'receitas' ? 'active' : '' ?>" href="<?= site_url('receitas') ?>">
                <i class="fa-solid fa-money-check-dollar"></i> Receitas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $atual === 'categorias' ? 'active' : '' ?>" href="<?= site_url('categorias') ?>">
                <i class="fa-solid fa-tags"></i> Categorias
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link disabled" href="#" title="Disponível na Etapa 4"><i class="fa-solid fa-calendar-days"></i> Agenda <span class="badge bg-secondary ms-auto">E4</span></a></li>
        <li class="nav-item"><a class="nav-link disabled" href="#" title="Disponível na Etapa 8"><i class="fa-solid fa-chart-bar"></i> Relatórios <span class="badge bg-secondary ms-auto">E8</span></a></li>
    </ul>
</aside>
