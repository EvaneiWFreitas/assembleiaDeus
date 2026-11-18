<?php
$rotulos = [
    'data'        => $config['eventos_detalhe_data'] ?? 'Data',
    'horario'     => $config['eventos_detalhe_horario'] ?? 'Horário',
    'local'       => $config['eventos_detalhe_local'] ?? 'Local',
    'congregacao' => $config['eventos_detalhe_congregacao'] ?? 'Congregação',
];
?>
<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= e(url('eventos')) ?>">Eventos</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($evento['titulo']) ?></li>
            </ol>
        </nav>

        <article class="card ad-card border-0 p-4 p-lg-5">
            <?php if (!empty($evento['foto'])): ?>
                <img src="<?= e(url('uploads/' . $evento['foto'])) ?>" class="img-fluid rounded mb-4" alt="<?= e($evento['titulo']) ?>">
            <?php endif; ?>

            <?php if (!empty($evento['tipo_nome'])): ?>
                <span class="badge ad-badge mb-3"><?= e($evento['tipo_nome']) ?></span>
            <?php endif; ?>
            <h1 class="ad-titulo-secao"><?= e($evento['titulo']) ?></h1>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><i class="far fa-calendar me-2"></i><strong><?= e($rotulos['data']) ?>:</strong> <?= e(dataExtenso((string) $evento['data_inicio'])) ?></div>
                <?php if (!empty($evento['horario'])): ?>
                    <div class="col-md-4"><i class="far fa-clock me-2"></i><strong><?= e($rotulos['horario']) ?>:</strong> <?= e(substr((string) $evento['horario'], 0, 5)) ?></div>
                <?php endif; ?>
                <?php if (!empty($evento['local_evento'])): ?>
                    <div class="col-md-4"><i class="fas fa-location-dot me-2"></i><strong><?= e($rotulos['local']) ?>:</strong> <?= e($evento['local_evento']) ?></div>
                <?php endif; ?>
                <?php if (!empty($evento['congregacao_nome'])): ?>
                    <div class="col-md-6"><i class="fas fa-place-of-worship me-2"></i><strong><?= e($rotulos['congregacao']) ?>:</strong> <?= e($evento['congregacao_nome']) ?></div>
                <?php endif; ?>
            </div>

            <div class="ad-conteudo-pagina">
                <?= nl2br(e((string) $evento['descricao'])) ?>
            </div>
        </article>
    </div>
</div>
