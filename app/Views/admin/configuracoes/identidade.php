<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url()) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver site
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <h2 class="h6 admin-card-titulo mb-4 text-start">Prévia da identidade</h2>
                <img src="<?= e($logoUrl) ?>" alt="Prévia do logotipo" class="admin-logo-preview mx-auto">
                <h4 class="mt-4 mb-1"><?= e($valores['ministerio_nome']) ?></h4>
                <span class="badge ad-badge"><?= e($valores['ministerio_sigla']) ?></span>
                <p class="small text-muted mt-3 mb-0">
                    <?= $valores['logotipo'] !== '' ? 'Logotipo personalizado ativo.' : 'Usando o logotipo padrão.' ?>
                </p>

                <?php if ($valores['logotipo'] !== ''): ?>
                    <hr class="my-4">
                    <form method="post" action="<?= e(url('admin/configuracoes/identidade/logo/remover')) ?>" class="admin-form"
                          onsubmit="return confirm('Remover o logotipo e voltar ao padrão?');">
                        <?= App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                            <i class="fas fa-rotate-left me-1"></i>Restaurar logotipo padrão
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <form method="post" action="<?= e(url('admin/configuracoes/identidade')) ?>" enctype="multipart/form-data" class="admin-form">
                    <?= App\Core\Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="campo_nome" class="form-label">Nome da igreja</label>
                            <input type="text" class="form-control" id="campo_nome" name="ministerio_nome"
                                   value="<?= e(old('ministerio_nome', $valores['ministerio_nome'])) ?>" required>
                            <div class="form-text">Exemplo: Assembleia de Deus</div>
                        </div>
                        <div class="col-md-4">
                            <label for="campo_sigla" class="form-label">Sigla</label>
                            <input type="text" class="form-control" id="campo_sigla" name="ministerio_sigla"
                                   value="<?= e(old('ministerio_sigla', $valores['ministerio_sigla'])) ?>" required>
                            <div class="form-text">Exemplo: AD</div>
                        </div>
                        <div class="col-12">
                            <label for="campo_logotipo" class="form-label">Logotipo</label>
                            <input type="file" class="form-control" id="campo_logotipo" name="logotipo"
                                   accept="image/jpeg,image/png,image/webp,image/svg+xml">
                            <div class="form-text">JPG, PNG, WebP ou SVG, até 2 MB. Deixe vazio para manter o logotipo atual.</div>
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
