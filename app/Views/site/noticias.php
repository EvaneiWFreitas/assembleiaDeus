<?php
$noticiasTituloPagina = (string) ($config['noticias_titulo'] ?? 'Notícias');
$noticiasEyebrow = (string) ($config['noticias_eyebrow'] ?? 'Acontecendo no ministério');
$noticiasVazio = (string) ($config['noticias_vazio'] ?? 'Nenhuma notícia publicada no momento.');
$noticiasLer = (string) ($config['noticias_ler'] ?? 'Ler notícia');
?>
<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($noticiasTituloPagina) ?></li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <?php if (trim($noticiasEyebrow) !== ''): ?>
                <span class="ad-eyebrow"><?= e($noticiasEyebrow) ?></span>
            <?php endif; ?>
            <?php if (trim($noticiasTituloPagina) !== ''): ?>
                <h1 class="ad-titulo-secao"><?= e($noticiasTituloPagina) ?></h1>
            <?php endif; ?>
        </div>

        <?php if ($noticias === []): ?>
            <?php if (trim($noticiasVazio) !== ''): ?>
                <p class="ad-vazio text-center"><?= e($noticiasVazio) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="ad-card ad-noticia-card h-100 p-0">
                            <?php App\Core\View::partial('noticia-midia', ['noticia' => $noticia, 'classeMidia' => 'mb-0', 'midiaPadrao' => true]); ?>
                            <div class="ad-noticia-corpo">
                                <small class="text-muted"><?= e(dataExtenso((string) ($noticia['publicada_em'] ?? $noticia['created_at']))) ?></small>
                                <h4 class="mt-2 ad-noticia-titulo"><?= e($noticia['titulo']) ?></h4>
                                <p class="small ad-noticia-resumo"><?= e($noticia['resumo'] ?? '') ?></p>
                                <?php if (trim($noticiasLer) !== ''): ?>
                                    <a href="<?= e(url('noticias/' . $noticia['slug'])) ?>" class="ad-noticia-link btn btn-sm btn-ad-outline"><?= e($noticiasLer) ?></a>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
