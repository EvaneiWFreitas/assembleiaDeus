<?php
$textos = [
    'eyebrow'      => $config['eventos_eyebrow'] ?? 'Agenda do ministério',
    'titulo'       => $config['eventos_titulo'] ?? 'Eventos e programação',
    'vazio'        => $config['eventos_vazio'] ?? 'Nenhum evento publicado no momento.',
    'ver_detalhes' => $config['eventos_ver_detalhes'] ?? 'Ver detalhes',
];
?>
<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Eventos</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="ad-eyebrow"><?= e($textos['eyebrow']) ?></span>
            <h1 class="ad-titulo-secao"><?= e($textos['titulo']) ?></h1>
        </div>

        <?php if ($eventos === []): ?>
            <p class="ad-vazio text-center"><?= e($textos['vazio']) ?></p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($eventos as $evento): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="ad-card h-100 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="ad-evento-data-inline">
                                    <strong><?= e(date('d', strtotime((string) $evento['data_inicio']))) ?></strong>
                                    <span><?= e(substr(dataExtenso((string) $evento['data_inicio']), 3, 3)) ?></span>
                                </div>
                                <?php if (!empty($evento['tipo_nome'])): ?>
                                    <span class="badge ad-badge"><?= e($evento['tipo_nome']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h4><?= e($evento['titulo']) ?></h4>
                            <p class="small mb-2"><i class="far fa-clock me-2"></i><?= e(date('d/m/Y H:i', strtotime((string) $evento['data_inicio']))) ?></p>
                            <?php if (!empty($evento['local_evento'])): ?>
                                <p class="small mb-2"><i class="fas fa-location-dot me-2"></i><?= e($evento['local_evento']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($evento['congregacao_nome'])): ?>
                                <p class="small mb-2"><i class="fas fa-place-of-worship me-2"></i><?= e($evento['congregacao_nome']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($evento['descricao'])): ?>
                                <p class="small mb-3"><?= e(mb_substr(strip_tags((string) $evento['descricao']), 0, 180)) ?></p>
                            <?php endif; ?>
                            <a href="<?= e(url('eventos/' . $evento['id'])) ?>" class="btn btn-sm btn-ad-outline mt-auto"><?= e($textos['ver_detalhes']) ?></a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
