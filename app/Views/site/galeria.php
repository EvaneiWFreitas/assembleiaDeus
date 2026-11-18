<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Galeria</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="ad-eyebrow">Memórias do ministério</span>
            <h1 class="ad-titulo-secao">Galeria de fotografias</h1>
        </div>

        <?php if ($fotos === []): ?>
            <p class="ad-vazio text-center">Nenhuma fotografia publicada no momento.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($fotos as $foto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <figure class="ad-galeria-item">
                            <a href="<?= e(url('uploads/' . $foto['imagem'])) ?>" data-gallery="galeria">
                                <img src="<?= e(url('uploads/' . $foto['imagem'])) ?>" class="img-fluid rounded" alt="<?= e($foto['titulo'] ?? 'Fotografia') ?>" loading="lazy">
                            </a>
                            <?php if (!empty($foto['titulo'])): ?>
                                <figcaption class="small text-muted mt-1"><?= e($foto['titulo']) ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
