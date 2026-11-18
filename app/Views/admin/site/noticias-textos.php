<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= e(url('admin/noticias')) ?>" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-newspaper me-1"></i>Gerenciar notícias
        </a>
        <a href="<?= e(url('noticias')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver notícias
        </a>
    </div>
</div>

<form method="post" action="<?= e(url('admin/site/noticias-textos')) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-newspaper me-2"></i>Página de notícias</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="campo_eyebrow" class="form-label">Subtítulo (opcional)</label>
                    <input type="text" class="form-control" id="campo_eyebrow" name="noticias_eyebrow"
                           value="<?= e(old('noticias_eyebrow', $textos['noticias_eyebrow'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_titulo" class="form-label">Título (opcional)</label>
                    <input type="text" class="form-control" id="campo_titulo" name="noticias_titulo"
                           value="<?= e(old('noticias_titulo', $textos['noticias_titulo'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_vazio" class="form-label">Mensagem quando não houver notícias</label>
                    <input type="text" class="form-control" id="campo_vazio" name="noticias_vazio"
                           value="<?= e(old('noticias_vazio', $textos['noticias_vazio'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_ler" class="form-label">Botão dos cards</label>
                    <input type="text" class="form-control" id="campo_ler" name="noticias_ler"
                           value="<?= e(old('noticias_ler', $textos['noticias_ler'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-house me-2"></i>Seção na página inicial</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="campo_home_eyebrow" class="form-label">Subtítulo (opcional)</label>
                    <input type="text" class="form-control" id="campo_home_eyebrow" name="noticias_home_eyebrow"
                           value="<?= e(old('noticias_home_eyebrow', $textos['noticias_home_eyebrow'])) ?>">
                </div>
                <div class="col-md-4">
                    <label for="campo_home_titulo" class="form-label">Título (opcional)</label>
                    <input type="text" class="form-control" id="campo_home_titulo" name="noticias_home_titulo"
                           value="<?= e(old('noticias_home_titulo', $textos['noticias_home_titulo'])) ?>">
                </div>
                <div class="col-md-4">
                    <label for="campo_home_botao" class="form-label">Botão (opcional)</label>
                    <input type="text" class="form-control" id="campo_home_botao" name="noticias_home_botao"
                           value="<?= e(old('noticias_home_botao', $textos['noticias_home_botao'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_home_vazio" class="form-label">Mensagem quando não houver notícias</label>
                    <input type="text" class="form-control" id="campo_home_vazio" name="noticias_home_vazio"
                           value="<?= e(old('noticias_home_vazio', $textos['noticias_home_vazio'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_home_ler" class="form-label">Link dos cards</label>
                    <input type="text" class="form-control" id="campo_home_ler" name="noticias_home_ler"
                           value="<?= e(old('noticias_home_ler', $textos['noticias_home_ler'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= e(url('admin/site')) ?>" class="btn btn-ad-outline">Cancelar</a>
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar textos
        </button>
    </div>
</form>
