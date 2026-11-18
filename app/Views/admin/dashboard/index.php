<?php
$cartoes = [
    ['label' => 'Igrejas matriz', 'valor' => $resumo['igrejas'], 'icone' => 'fas fa-church', 'cor' => 'primary', 'permissao' => 'igrejas.ver'],
    ['label' => 'Membros ativos', 'valor' => $resumo['membros_ativos'], 'icone' => 'fas fa-users', 'cor' => 'info', 'permissao' => 'membros.ver'],
    ['label' => 'Obreiros ativos', 'valor' => $resumo['obreiros_ativos'], 'icone' => 'fas fa-user-tie', 'cor' => 'success', 'permissao' => 'obreiros.ver'],
    ['label' => 'Congregações', 'valor' => $resumo['congregacoes'], 'icone' => 'fas fa-place-of-worship', 'cor' => 'primary', 'permissao' => 'congregacoes.ver'],
    ['label' => 'Pontos de pregação', 'valor' => $resumo['pontos_pregacao'], 'icone' => 'fas fa-map-marker-alt', 'cor' => 'warning', 'permissao' => 'pontos_pregacao.ver'],
    ['label' => 'Mensagens não lidas', 'valor' => $resumo['contatos_nao_lidos'] ?? 0, 'icone' => 'fas fa-inbox', 'cor' => 'danger', 'permissao' => 'site_contatos.ver', 'rota' => 'contatos'],
];

$acoesRapidas = [
    ['label' => 'Nova congregação', 'icone' => 'fas fa-place-of-worship', 'rota' => 'congregacoes/novo', 'permissao' => 'congregacoes.criar'],
    ['label' => 'Novo ponto', 'icone' => 'fas fa-map-marker-alt', 'rota' => 'pontos/novo', 'permissao' => 'pontos_pregacao.criar'],
    ['label' => 'Cadastrar membro', 'icone' => 'fas fa-user-plus', 'rota' => 'membros/novo', 'permissao' => 'membros.criar'],
    ['label' => 'Cadastrar obreiro', 'icone' => 'fas fa-user-tie', 'rota' => 'obreiros/novo', 'permissao' => 'obreiros.criar'],
    ['label' => 'Arquivar obreiro', 'icone' => 'fas fa-box-archive', 'rota' => 'arquivo-morto/arquivar', 'permissao' => 'arquivo_morto.arquivar'],
    ['label' => 'Nossa História', 'icone' => 'fas fa-book', 'rota' => 'ministerio/historia', 'permissao' => 'site.editar'],
    ['label' => 'Palavra Pastoral', 'icone' => 'fas fa-bible', 'rota' => 'ministerio/palavra-pastoral', 'permissao' => 'site.editar'],
    ['label' => 'Cultos e programação', 'icone' => 'fas fa-clock', 'rota' => 'horarios', 'permissao' => 'site.editar'],
    ['label' => 'Eventos e programação', 'icone' => 'fas fa-calendar-days', 'rota' => 'eventos', 'permissao' => 'eventos.ver'],
    ['label' => 'Identidade da igreja', 'icone' => 'fas fa-building-columns', 'rota' => 'configuracoes/identidade', 'permissao' => 'site.editar'],
    ['label' => 'Slides da home', 'icone' => 'fas fa-images', 'rota' => 'site', 'permissao' => 'site.editar'],
    ['label' => 'Textos do carrossel', 'icone' => 'fas fa-pen-nib', 'rota' => 'site/carrossel-textos', 'permissao' => 'site.editar'],
    ['label' => 'Gerenciar notícias', 'icone' => 'fas fa-newspaper', 'rota' => 'noticias', 'permissao' => 'site_noticias.ver'],
    ['label' => 'Textos das notícias', 'icone' => 'fas fa-pen-nib', 'rota' => 'site/noticias-textos', 'permissao' => 'site.editar'],
    ['label' => 'Editar história na home',  'icone' => 'fas fa-book-open', 'rota' => 'site/historia', 'permissao' => 'site.editar'],
    ['label' => 'Mensagens de contato', 'icone' => 'fas fa-inbox', 'rota' => 'contatos', 'permissao' => 'site_contatos.ver'],
    ['label' => 'Ver usuários', 'icone' => 'fas fa-users-gear', 'rota' => 'usuarios', 'permissao' => 'seguranca.usuarios_ver'],
];
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1">Dashboard</h1>
        <p class="text-muted small mb-0">Visão geral do ministério e atalhos disponíveis para seu perfil.</p>
    </div>
    <span class="admin-data-atual"><i class="fas fa-calendar-day me-1"></i><?= e(dataExtenso(date('Y-m-d'))) ?></span>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cartoes as $cartao): ?>
        <?php if (!can((string) $cartao['permissao'])) { continue; } ?>
        <?php $temLink = isset($cartao['rota']) && (string) $cartao['rota'] !== ''; ?>
        <div class="col-6 col-lg-4 col-xl-3">
            <?php if ($temLink): ?>
                <a href="<?= e(url('admin/' . (string) $cartao['rota'])) ?>"
                   class="d-block h-100 text-decoration-none"
                   aria-label="Abrir <?= e((string) $cartao['label']) ?>">
            <?php endif; ?>
                <div class="card admin-stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="admin-stat-icone text-<?= e($cartao['cor']) ?>">
                            <i class="<?= e($cartao['icone']) ?>"></i>
                        </div>
                        <div class="admin-stat-valor"><?= e($cartao['valor']) ?></div>
                        <div class="admin-stat-label"><?= e($cartao['label']) ?></div>
                    </div>
                </div>
            <?php if ($temLink): ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 admin-card-titulo mb-0">Próximos eventos</h2>
                </div>

                <?php if ($eventos === []): ?>
                    <p class="admin-vazio mb-0"><i class="fas fa-calendar-xmark me-2"></i>Nenhum evento programado.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush admin-lista-eventos">
                        <?php foreach ($eventos as $evento): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex gap-3">
                                    <div class="admin-evento-data">
                                        <strong><?= date('d', strtotime($evento['data_inicio'])) ?></strong>
                                        <small><?= e(strtoupper(date('M', strtotime($evento['data_inicio'])))) ?></small>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= e($evento['titulo']) ?></div>
                                        <div class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($evento['data_inicio'])) ?>
                                            <?php if (!empty($evento['local_evento'])): ?>
                                                · <?= e($evento['local_evento']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Acessos recentes</h2>
                <?php if ($acessos === []): ?>
                    <p class="admin-vazio mb-0"><i class="fas fa-clock-rotate-left me-2"></i>Sem registros de acesso.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($acessos as $acesso): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between gap-3">
                                <div>
                                    <div class="small fw-semibold"><?= e(str_replace('_', ' ', $acesso['acao'])) ?></div>
                                    <div class="text-muted small"><?= e($acesso['detalhes'] ?: '—') ?></div>
                                </div>
                                <time class="text-muted small text-nowrap">
                                    <?= date('d/m H:i', strtotime($acesso['created_at'])) ?>
                                </time>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <h2 class="h6 admin-card-titulo mb-3">Atalhos</h2>
        <div class="d-flex flex-wrap gap-2">
            <?php $visiveis = 0; ?>
            <?php foreach ($acoesRapidas as $acao): ?>
                <?php if (!can((string) $acao['permissao'])) { continue; } ?>
                <?php $visiveis++; ?>
                <a class="btn btn-sm btn-ad-outline" href="<?= e(url('admin/' . $acao['rota'])) ?>">
                    <i class="<?= e($acao['icone']) ?> me-1"></i><?= e($acao['label']) ?>
                </a>
            <?php endforeach; ?>
            <?php if ($visiveis === 0): ?>
                <p class="admin-vazio mb-0">Nenhum atalho disponível para seu perfil.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
