<?php
/**
 * Sidebar — menu tipo acordeon com seções colapsáveis.
 * Seções abrem automaticamente quando o item ativo pertence a ela.
 */
$atual = strtolower(service('uri')->getSegment(1) ?: 'dashboard');

// Mapeia cada página para sua seção pai
$mapaSecoes = [
    'usuarios'    => 'admin', 'roles'       => 'admin',
    'igreja'      => 'igreja', 'banners'    => 'igreja', 'congregacoes' => 'igreja',
    'membros'     => 'pessoas', 'visitantes' => 'pessoas', 'obreiros' => 'pessoas', 'carteirinhas' => 'pessoas',
    'cargos'      => 'ministerio', 'departamentos' => 'ministerio', 'ministerios' => 'ministerio',
    'celulas'     => 'ministerio', 'discipulados'  => 'ministerio', 'cursos' => 'ministerio',
    'inscricoes'  => 'ministerio',
    'financeiro'  => 'financeiro', 'dizimos' => 'financeiro', 'ofertas' => 'financeiro',
    'despesas'    => 'financeiro', 'receitas' => 'financeiro', 'categorias' => 'financeiro',
    'agenda'      => 'operacional', 'relatorios' => 'operacional',
];

$secaoAtiva = $mapaSecoes[$atual] ?? 'inicio';

function ativa(int $id, string $secaoAtiva, string $secao): string
{
    return ($secaoAtiva === $secao) ? 'show' : '';
}

function colapsou(int $id, string $secaoAtiva, string $secao): string
{
    return ($secaoAtiva === $secao) ? '' : 'collapsed';
}
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-scroll">
        <ul class="nav flex-column p-2">

            <!-- ===== INÍCIO ===== -->
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

            <!-- ===== ADMINISTRAÇÃO ===== -->
            <?php if (tem_permissao('usuarios', 'visualizar') || tem_permissao('roles', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'admin') ?>" data-bs-toggle="collapse" href="#sec-admin">
                    <i class="fa-solid fa-gear"></i> Administração
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'admin') ?>" id="sec-admin" data-bs-parent="#appSidebar">
                    <?php if (tem_permissao('usuarios', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'usuarios' ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">
                        <i class="fa-solid fa-user-gear"></i> Usuários
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('roles', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'roles' ? 'active' : '' ?>" href="<?= site_url('roles') ?>">
                        <i class="fa-solid fa-shield-halved"></i> Perfis & Permissões
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- ===== IGREJA ===== -->
            <?php if (tem_permissao('igreja', 'visualizar') || tem_permissao('banners', 'visualizar') || tem_permissao('congregacoes', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'igreja') ?>" data-bs-toggle="collapse" href="#sec-igreja">
                    <i class="fa-solid fa-church"></i> Igreja
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'igreja') ?>" id="sec-igreja" data-bs-parent="#appSidebar">
                    <?php if (tem_permissao('igreja', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'igreja' ? 'active' : '' ?>" href="<?= site_url('igreja') ?>">
                        <i class="fa-solid fa-church"></i> Dados da Igreja
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('banners', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'banners' ? 'active' : '' ?>" href="<?= site_url('banners') ?>">
                        <i class="fa-solid fa-images"></i> Banners (Site)
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('congregacoes', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'congregacoes' ? 'active' : '' ?>" href="<?= site_url('congregacoes') ?>">
                        <i class="fa-solid fa-place-of-worship"></i> Congregações
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- ===== PESSOAS ===== -->
            <?php if (tem_permissao('membros', 'visualizar') || tem_permissao('visitantes', 'visualizar') || tem_permissao('obreiros', 'visualizar') || tem_permissao('carteirinhas', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'pessoas') ?>" data-bs-toggle="collapse" href="#sec-pessoas">
                    <i class="fa-solid fa-people-group"></i> Pessoas
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'pessoas') ?>" id="sec-pessoas" data-bs-parent="#appSidebar">
                    <?php if (tem_permissao('membros', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'membros' ? 'active' : '' ?>" href="<?= site_url('membros') ?>">
                        <i class="fa-solid fa-people-group"></i> Membros
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('visitantes', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'visitantes' ? 'active' : '' ?>" href="<?= site_url('visitantes') ?>">
                        <i class="fa-solid fa-user-plus"></i> Visitantes
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('obreiros', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'obreiros' ? 'active' : '' ?>" href="<?= site_url('obreiros') ?>">
                        <i class="fa-solid fa-user-tie"></i> Pastores & Obreiros
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('carteirinhas', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'carteirinhas' ? 'active' : '' ?>" href="<?= site_url('carteirinhas') ?>">
                        <i class="fa-solid fa-id-card"></i> Carteirinhas
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- ===== MINISTÉRIO ===== -->
            <?php if (tem_permissao('cargos', 'visualizar') || tem_permissao('departamentos', 'visualizar') || tem_permissao('ministerios', 'visualizar') || tem_permissao('celulas', 'visualizar') || tem_permissao('discipulados', 'visualizar') || tem_permissao('cursos', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'ministerio') ?>" data-bs-toggle="collapse" href="#sec-ministerio">
                    <i class="fa-solid fa-book-bible"></i> Ministério
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'ministerio') ?>" id="sec-ministerio" data-bs-parent="#appSidebar">
                    <?php if (tem_permissao('cargos', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'cargos' ? 'active' : '' ?>" href="<?= site_url('cargos') ?>">
                        <i class="fa-solid fa-briefcase"></i> Cargos de Obreiros
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('departamentos', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'departamentos' ? 'active' : '' ?>" href="<?= site_url('departamentos') ?>">
                        <i class="fa-solid fa-sitemap"></i> Departamentos
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('ministerios', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'ministerios' ? 'active' : '' ?>" href="<?= site_url('ministerios') ?>">
                        <i class="fa-solid fa-hands-praying"></i> Ministérios
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('celulas', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'celulas' ? 'active' : '' ?>" href="<?= site_url('celulas') ?>">
                        <i class="fa-solid fa-house-chimney-user"></i> Células
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('discipulados', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'discipulados' ? 'active' : '' ?>" href="<?= site_url('discipulados') ?>">
                        <i class="fa-solid fa-hands-holding-circle"></i> Discipulado
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('cursos', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= in_array($atual, ['cursos', 'inscricoes']) ? 'active' : '' ?>" href="<?= site_url('cursos') ?>">
                        <i class="fa-solid fa-book-bible"></i> Cursos
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <!-- ===== FINANCEIRO ===== -->
            <?php if (tem_permissao('financeiro', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'financeiro') ?>" data-bs-toggle="collapse" href="#sec-financeiro">
                    <i class="fa-solid fa-coins"></i> Financeiro
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'financeiro') ?>" id="sec-financeiro" data-bs-parent="#appSidebar">
                    <a class="nav-link sub-link <?= $atual === 'financeiro' ? 'active' : '' ?>" href="<?= site_url('financeiro') ?>">
                        <i class="fa-solid fa-chart-line"></i> Painel
                    </a>
                    <a class="nav-link sub-link <?= $atual === 'dizimos' ? 'active' : '' ?>" href="<?= site_url('dizimos') ?>">
                        <i class="fa-solid fa-hand-holding-dollar"></i> Dízimos
                    </a>
                    <a class="nav-link sub-link <?= $atual === 'ofertas' ? 'active' : '' ?>" href="<?= site_url('ofertas') ?>">
                        <i class="fa-solid fa-box-open"></i> Ofertas
                    </a>
                    <a class="nav-link sub-link <?= $atual === 'despesas' ? 'active' : '' ?>" href="<?= site_url('despesas') ?>">
                        <i class="fa-solid fa-money-bill-wave"></i> Despesas
                    </a>
                    <a class="nav-link sub-link <?= $atual === 'receitas' ? 'active' : '' ?>" href="<?= site_url('receitas') ?>">
                        <i class="fa-solid fa-money-check-dollar"></i> Receitas
                    </a>
                    <a class="nav-link sub-link <?= $atual === 'categorias' ? 'active' : '' ?>" href="<?= site_url('categorias') ?>">
                        <i class="fa-solid fa-tags"></i> Categorias
                    </a>
                </div>
            </li>
            <?php endif; ?>

            <!-- ===== OPERACIONAL ===== -->
            <?php if (tem_permissao('agenda', 'visualizar') || tem_permissao('relatorios', 'visualizar')): ?>
            <li class="nav-item accordion-item">
                <a class="accordion-button nav-link <?= colapsou(0, $secaoAtiva, 'operacional') ?>" data-bs-toggle="collapse" href="#sec-operacional">
                    <i class="fa-solid fa-clipboard-list"></i> Operacional
                </a>
                <div class="collapse <?= ativa(0, $secaoAtiva, 'operacional') ?>" id="sec-operacional" data-bs-parent="#appSidebar">
                    <?php if (tem_permissao('agenda', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'agenda' ? 'active' : '' ?>" href="<?= site_url('agenda') ?>">
                        <i class="fa-solid fa-calendar-days"></i> Agenda
                    </a>
                    <?php endif; ?>
                    <?php if (tem_permissao('relatorios', 'visualizar')): ?>
                    <a class="nav-link sub-link <?= $atual === 'relatorios' ? 'active' : '' ?>" href="<?= site_url('relatorios') ?>">
                        <i class="fa-solid fa-chart-bar"></i> Relatórios
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</aside>
