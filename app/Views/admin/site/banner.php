<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url()) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver site
    </a>
</div>

<div class="alert alert-light border d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-pen-nib text-primary"></i>
        <span class="small mb-0">Precisa alterar as palavras, os textos sobre a imagem ou as cores?</span>
    </div>
    <a href="<?= e(url('admin/site/carrossel-textos')) ?>" class="btn btn-sm btn-ad-outline">
        Gerenciar textos e cores <i class="fas fa-arrow-right ms-1"></i>
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 admin-card-titulo mb-0">Adicionar slides</h2>
                    <span class="admin-slide-contador"><?= count($slides) ?>/<?= (int) $limite ?></span>
                </div>
                <p class="small text-muted mb-4">Envie uma ou mais imagens. Elas aparecerão em sequência no carrossel da página inicial.</p>

                <?php if (count($slides) >= (int) $limite): ?>
                    <div class="alert alert-warning small mb-0">
                        Limite de <?= (int) $limite ?> slides atingido. Remova um slide para adicionar outro.
                    </div>
                <?php else: ?>
                    <form method="post" action="<?= e(url('admin/site')) ?>" enctype="multipart/form-data" class="admin-form">
                        <?= App\Core\Csrf::field() ?>

                        <div class="mb-4">
                            <label for="banner" class="form-label">Imagens</label>
                            <input type="file" class="form-control" id="banner" name="banner[]"
                                   accept="image/jpeg,image/png,image/webp" multiple required>
                            <div class="form-text">JPG, PNG ou WebP, até 2 MB cada. Máximo de <?= (int) $limite ?> slides.</div>
                        </div>

                        <button type="submit" class="btn btn-ad-primary">
                            <i class="fas fa-upload me-1"></i>Adicionar ao carrossel
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Slides cadastrados</h2>

                <?php if ($slides === []): ?>
                    <div class="admin-vazio text-center py-5">
                        <i class="fas fa-images fa-2x mb-3 d-block"></i>
                        Nenhum slide cadastrado. O site está usando a imagem padrão.
                    </div>
                <?php else: ?>
                    <div class="admin-slides-lista">
                        <?php foreach ($slides as $indice => $slide): ?>
                            <div class="admin-slide-item">
                                <img src="<?= e($slide['url']) ?>" alt="Slide <?= (int) $indice + 1 ?>" class="admin-slide-miniatura">
                                <div class="admin-slide-info">
                                    <strong>Slide <?= (int) $indice + 1 ?></strong>
                                    <small class="d-block text-muted text-truncate"><?= e(basename($slide['caminho'])) ?></small>
                                </div>
                                <form method="post" action="<?= e(url('admin/site/banner/remover-slide')) ?>" class="admin-form">
                                    <?= App\Core\Csrf::field() ?>
                                    <input type="hidden" name="indice" value="<?= (int) $indice ?>">
                                    <button type="submit" class="btn btn-sm btn-ad-outline text-danger"
                                            aria-label="Remover slide <?= (int) $indice + 1 ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">
                    <form method="post" action="<?= e(url('admin/site/banner/remover')) ?>" class="admin-form"
                          onsubmit="return confirm('Remover todos os slides e voltar à imagem padrão?');">
                        <?= App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                            <i class="fas fa-rotate-left me-1"></i>Restaurar imagem padrão
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
