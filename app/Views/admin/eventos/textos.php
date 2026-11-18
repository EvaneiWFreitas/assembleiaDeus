<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/eventos')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar aos eventos
    </a>
</div>

<form method="post" action="<?= e(url('admin/eventos/textos')) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-calendar-days me-2"></i>Página de eventos</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="campo_eyebrow" class="form-label">Subtítulo</label>
                    <input type="text" class="form-control" id="campo_eyebrow" name="eventos_eyebrow"
                           value="<?= e(old('eventos_eyebrow', $textos['eventos_eyebrow'])) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="campo_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_titulo" name="eventos_titulo"
                           value="<?= e(old('eventos_titulo', $textos['eventos_titulo'])) ?>" required>
                </div>
                <div class="col-md-8">
                    <label for="campo_vazio" class="form-label">Mensagem quando não houver eventos</label>
                    <input type="text" class="form-control" id="campo_vazio" name="eventos_vazio"
                           value="<?= e(old('eventos_vazio', $textos['eventos_vazio'])) ?>">
                </div>
                <div class="col-md-4">
                    <label for="campo_ver_detalhes" class="form-label">Botão dos cards</label>
                    <input type="text" class="form-control" id="campo_ver_detalhes" name="eventos_ver_detalhes"
                           value="<?= e(old('eventos_ver_detalhes', $textos['eventos_ver_detalhes'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-circle-info me-2"></i>Página de detalhes do evento</h2>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="campo_detalhe_data" class="form-label">Rótulo Data</label>
                    <input type="text" class="form-control" id="campo_detalhe_data" name="eventos_detalhe_data"
                           value="<?= e(old('eventos_detalhe_data', $textos['eventos_detalhe_data'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="campo_detalhe_horario" class="form-label">Rótulo Horário</label>
                    <input type="text" class="form-control" id="campo_detalhe_horario" name="eventos_detalhe_horario"
                           value="<?= e(old('eventos_detalhe_horario', $textos['eventos_detalhe_horario'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="campo_detalhe_local" class="form-label">Rótulo Local</label>
                    <input type="text" class="form-control" id="campo_detalhe_local" name="eventos_detalhe_local"
                           value="<?= e(old('eventos_detalhe_local', $textos['eventos_detalhe_local'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="campo_detalhe_congregacao" class="form-label">Rótulo Congregação</label>
                    <input type="text" class="form-control" id="campo_detalhe_congregacao" name="eventos_detalhe_congregacao"
                           value="<?= e(old('eventos_detalhe_congregacao', $textos['eventos_detalhe_congregacao'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-house me-2"></i>Seção na página inicial</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="campo_home_eyebrow" class="form-label">Subtítulo</label>
                    <input type="text" class="form-control" id="campo_home_eyebrow" name="eventos_home_eyebrow"
                           value="<?= e(old('eventos_home_eyebrow', $textos['eventos_home_eyebrow'])) ?>">
                </div>
                <div class="col-md-4">
                    <label for="campo_home_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_home_titulo" name="eventos_home_titulo"
                           value="<?= e(old('eventos_home_titulo', $textos['eventos_home_titulo'])) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="campo_home_botao" class="form-label">Botão</label>
                    <input type="text" class="form-control" id="campo_home_botao" name="eventos_home_botao"
                           value="<?= e(old('eventos_home_botao', $textos['eventos_home_botao'])) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_home_vazio" class="form-label">Mensagem quando não houver eventos</label>
                    <input type="text" class="form-control" id="campo_home_vazio" name="eventos_home_vazio"
                           value="<?= e(old('eventos_home_vazio', $textos['eventos_home_vazio'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="campo_home_local" class="form-label">Local padrão</label>
                    <input type="text" class="form-control" id="campo_home_local" name="eventos_home_local"
                           value="<?= e(old('eventos_home_local', $textos['eventos_home_local'])) ?>">
                </div>
                <div class="col-md-3">
                    <label for="campo_home_detalhes" class="form-label">Link dos cards</label>
                    <input type="text" class="form-control" id="campo_home_detalhes" name="eventos_home_ver_detalhes"
                           value="<?= e(old('eventos_home_ver_detalhes', $textos['eventos_home_ver_detalhes'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= e(url('admin/eventos')) ?>" class="btn btn-ad-outline">Cancelar</a>
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar textos
        </button>
    </div>
</form>
