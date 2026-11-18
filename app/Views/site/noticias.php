<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notícias</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="ad-eyebrow">Acontecendo no ministério</span>
            <h1 class="ad-titulo-secao">Notícias</h1>
        </div>

        <?php if ($noticias === []): ?>
            <p class="ad-vazio text-center">Nenhuma notícia publicada no momento.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="ad-card h-100 p-0">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <img src="<?= e(url('uploads/' . $noticia['imagem'])) ?>" class="card-img-top ad-card-img" alt="<?= e($noticia['titulo']) ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="p-4">
                                <small class="text-muted"><?= e(dataExtenso((string) ($noticia['publicada_em'] ?? $noticia['created_at']))) ?></small>
                                <h4 class="mt-2"><?= e($noticia['titulo']) ?></h4>
                                <p class="small mb-3"><?= e($noticia['resumo'] ?? '') ?></p>
                                <a href="<?= e(url('noticias/' . $noticia['slug'])) ?>" class="btn btn-sm btn-ad-outline">Ler notícia</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
