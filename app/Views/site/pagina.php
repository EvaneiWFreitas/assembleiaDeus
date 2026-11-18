<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($pagina['titulo']) ?></li>
            </ol>
        </nav>

        <div class="card ad-card border-0 p-4 p-lg-5">
            <span class="ad-eyebrow">Ministério</span>
            <h1 class="ad-titulo-secao"><?= e($pagina['titulo']) ?></h1>
            <?php if (($pagina['slug'] ?? '') === 'palavra-pastoral' && !empty($config['pastor_foto'])): ?>
                <div class="text-center mb-4">
                    <div class="ad-pastor-foto">
                        <img src="<?= e(url($config['pastor_foto'])) ?>" alt="Foto do Pastor Presidente">
                    </div>
                    <?php if (!empty($config['pastor_presidente'])): ?>
                        <h4 class="mt-3 mb-1"><?= e($config['pastor_presidente']) ?></h4>
                    <?php endif; ?>
                    <span class="text-muted">Pastor Presidente</span>
                </div>
            <?php endif; ?>
            <?php if (!empty($pagina['imagem'])): ?>
                <img src="<?= e(url($pagina['imagem'])) ?>" alt="<?= e($pagina['titulo']) ?>" class="img-fluid rounded ad-img-secao mb-4">
            <?php endif; ?>
            <div class="ad-conteudo-pagina">
                <?= $pagina['conteudo'] ?>
            </div>
        </div>
    </div>
</div>
