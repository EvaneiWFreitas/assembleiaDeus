<?php
$icones = [
    'igrejas'      => 'fas fa-church',
    'congregacoes' => 'fas fa-place-of-worship',
    'pontos'       => 'fas fa-map-marker-alt',
];
?>
<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($title) ?></li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <i class="<?= e($icones[$tipo] ?? 'fas fa-church') ?> fa-3x ad-icone-pagina mb-3"></i>
            <h1 class="ad-titulo-secao"><?= e($title) ?></h1>
        </div>

        <?php if ($itens === []): ?>
            <p class="ad-vazio text-center">Nenhum registro cadastrado até o momento.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($itens as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="ad-card h-100 p-0">
                            <?php if (!empty($item['foto'])): ?>
                                <img src="<?= e(url('uploads/' . $item['foto'])) ?>" class="card-img-top ad-card-img" alt="<?= e($item['nome'] ?? '') ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="p-4">
                                <h4><?= e($item['nome'] ?? '') ?></h4>
                                <?php if (isset($item['codigo'])): ?>
                                    <p class="small text-muted mb-2">Código: <?= e($item['codigo']) ?></p>
                                <?php endif; ?>
                                <p class="small mb-2"><i class="fas fa-location-dot me-2"></i><?= e(trim(($item['endereco'] ?? '') . ' - ' . ($item['cidade'] ?? '') . '/' . ($item['estado'] ?? ''), ' -')) ?></p>
                                <?php if (!empty($item['igreja_nome'])): ?>
                                    <p class="small mb-2"><i class="fas fa-church me-2"></i><?= e($item['igreja_nome']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['congregacao_nome'])): ?>
                                    <p class="small mb-2"><i class="fas fa-place-of-worship me-2"></i><?= e($item['congregacao_nome']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['telefone'])): ?>
                                    <p class="small mb-2"><i class="fas fa-phone me-2"></i><?= e($item['telefone']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['responsavel_nome'])): ?>
                                    <p class="small mb-0"><i class="fas fa-user me-2"></i><?= e($item['responsavel_nome']) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
