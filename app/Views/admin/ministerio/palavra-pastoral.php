<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('palavra-pastoral')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver página
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <h2 class="h6 admin-card-titulo mb-3 text-start">Foto do Pastor Presidente</h2>
                <img src="<?= e($previewPastor) ?>" alt="Prévia da foto do pastor" class="admin-upload-preview mx-auto">
                <p class="small text-muted mt-3 mb-0">
                    <?= $fotoPastor !== '' ? 'Foto personalizada ativa.' : 'Usando o ícone padrão.' ?>
                </p>

                <?php if ($fotoPastor !== ''): ?>
                    <hr class="my-4">
                    <form method="post" action="<?= e(url('admin/ministerio/palavra-pastoral/foto/remover')) ?>" class="admin-form"
                          onsubmit="return confirm('Remover a foto do pastor?');">
                        <?= App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                            <i class="fas fa-rotate-left me-1"></i>Remover foto
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="card admin-card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Imagem da mensagem</h2>
                <img src="<?= e($previewPagina) ?>" alt="Prévia da imagem da mensagem" class="admin-upload-preview">
                <p class="small text-muted mt-3 mb-0">
                    <?= $pagina['imagem'] !== '' ? 'Imagem personalizada ativa.' : 'Nenhuma imagem cadastrada.' ?>
                </p>

                <?php if ($pagina['imagem'] !== ''): ?>
                    <hr class="my-4">
                    <form method="post" action="<?= e(url('admin/ministerio/palavra-pastoral/imagem/remover')) ?>" class="admin-form"
                          onsubmit="return confirm('Remover a imagem da mensagem?');">
                        <?= App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                            <i class="fas fa-trash me-1"></i>Remover imagem
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url('admin/ministerio/palavra-pastoral')) ?>" enctype="multipart/form-data" class="admin-form">
                    <?= App\Core\Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="campo_titulo" class="form-label">Título da página</label>
                            <input type="text" class="form-control" id="campo_titulo" name="titulo"
                                   value="<?= e(old('titulo', $pagina['titulo'])) ?>" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="campo_ativa"
                                       name="ativa" value="1" <?= old('ativa', $pagina['ativa'] ? '1' : '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="campo_ativa">Página ativa</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="campo_pastor" class="form-label">Nome do Pastor Presidente</label>
                            <input type="text" class="form-control" id="campo_pastor" name="pastor_nome"
                                   value="<?= e(old('pastor_nome', $pastorNome)) ?>"
                                   placeholder="Pastor Presidente">
                        </div>
                        <div class="col-md-6">
                            <label for="campo_meta" class="form-label">Meta descrição</label>
                            <input type="text" class="form-control" id="campo_meta" name="meta_descricao"
                                   value="<?= e(old('meta_descricao', $pagina['meta_descricao'])) ?>">
                        </div>

                        <div class="col-12">
                            <label for="campo_conteudo" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="campo_conteudo" name="conteudo" rows="12" required><?= e(old('conteudo', $pagina['conteudo'])) ?></textarea>
                            <div class="form-text">HTML simples permitido: parágrafos, títulos, listas, links, negrito, itálico e imagens.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="campo_foto_pastor" class="form-label">Foto do Pastor Presidente</label>
                            <input type="file" class="form-control" id="campo_foto_pastor" name="foto_pastor"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a foto atual.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="campo_imagem" class="form-label">Imagem da mensagem</label>
                            <input type="file" class="form-control" id="campo_imagem" name="imagem"
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
