<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('#historia')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver seção
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Imagem da seção</h2>
                <img src="<?= e($preview) ?>" alt="Prévia da imagem da história" class="admin-upload-preview">
                <p class="small text-muted mt-3 mb-0">
                    <?= $imagem !== '' ? 'Imagem personalizada ativa.' : 'Usando a ilustração padrão.' ?>
                </p>

                <?php if ($imagem !== ''): ?>
                    <hr class="my-4">
                    <form method="post" action="<?= e(url('admin/site/historia/imagem/remover')) ?>" class="admin-form"
                          onsubmit="return confirm('Remover a imagem e voltar à ilustração padrão?');">
                        <?= App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                            <i class="fas fa-rotate-left me-1"></i>Restaurar imagem padrão
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url('admin/site/historia')) ?>" enctype="multipart/form-data" class="admin-form">
                    <?= App\Core\Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="campo_eyebrow" class="form-label">Subtítulo</label>
                            <input type="text" class="form-control" id="campo_eyebrow" name="historia_eyebrow"
                                   value="<?= e(old('historia_eyebrow', $valores['eyebrow'])) ?>" required>
                            <div class="form-text">Exemplo: Nossa história</div>
                        </div>

                        <div class="col-md-6">
                            <label for="campo_titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="campo_titulo" name="historia_titulo"
                                   value="<?= e(old('historia_titulo', $valores['titulo'])) ?>" required>
                            <div class="form-text">Exemplo: O que Deus tem feito entre nós</div>
                        </div>

                        <div class="col-12">
                            <label for="campo_texto" class="form-label">Texto resumido</label>
                            <textarea class="form-control" id="campo_texto" name="historia_texto" rows="7"
                                      placeholder="Escreva o resumo que aparece na página inicial."><?= e(old('historia_texto', $valores['texto'])) ?></textarea>
                            <div class="form-text">Se deixado vazio, o site usa o conteúdo da página História.</div>
                        </div>

                        <div class="col-12">
                            <label for="campo_imagem" class="form-label">Trocar imagem</label>
                            <input type="file" class="form-control" id="campo_imagem" name="historia_imagem"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a imagem atual.</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                        <a href="<?= e(url('admin/dashboard')) ?>" class="btn btn-ad-outline">Cancelar</a>
                        <button type="submit" class="btn btn-ad-primary">
                            <i class="fas fa-check me-1"></i>Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
