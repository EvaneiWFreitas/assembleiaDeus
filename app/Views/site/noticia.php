<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= e(url('noticias')) ?>">Notícias</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($noticia['titulo']) ?></li>
            </ol>
        </nav>

        <article class="card ad-card border-0 p-4 p-lg-5">
            <?php if (!empty($noticia['imagem'])): ?>
                <img src="<?= e(url('uploads/' . $noticia['imagem'])) ?>" class="img-fluid rounded mb-4" alt="<?= e($noticia['titulo']) ?>">
            <?php endif; ?>

            <small class="text-muted"><i class="far fa-calendar me-1"></i><?= e(dataExtenso((string) ($noticia['publicada_em'] ?? $noticia['created_at']))) ?></small>
            <h1 class="ad-titulo-secao mt-2"><?= e($noticia['titulo']) ?></h1>

            <?php if (!empty($noticia['resumo'])): ?>
                <p class="lead"><?= e($noticia['resumo']) ?></p>
            <?php endif; ?>

            <div class="ad-conteudo-pagina">
                <?= $noticia['conteudo'] ?>
            </div>
        </article>
    </div>
</div>
