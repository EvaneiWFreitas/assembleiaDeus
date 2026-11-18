<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0">Restaurar <?= e($arquivo['nome_completo']) ?> do arquivo morto.</p>
    </div>
    <a href="<?= e(url('admin/arquivo-morto/' . (int) $arquivo['id'])) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card admin-card border-0 shadow-sm">
    <form method="post" action="<?= e(url('admin/arquivo-morto/' . (int) $arquivo['id'] . '/restaurar')) ?>">
        <?= App\Core\Csrf::field() ?>
        <div class="card-body p-4">
            <div class="alert alert-warning mb-4">
                <i class="fas fa-triangle-exclamation me-2"></i>
                Esta ação reativa o obreiro, altera sua situação para <strong>Ativo</strong> e registra o retorno no histórico ministerial.
            </div>
            <dl class="row mb-0 small">
                <dt class="col-sm-4">Obreiro</dt>
                <dd class="col-sm-8"><?= e($arquivo['nome_completo']) ?> (<?= e($arquivo['codigo']) ?>)</dd>
                <dt class="col-sm-4">Motivo do arquivamento</dt>
                <dd class="col-sm-8"><?= e($arquivo['motivo']) ?></dd>
                <dt class="col-sm-4">Data do desligamento</dt>
                <dd class="col-sm-8"><?= e(date('d/m/Y', strtotime((string) $arquivo['data_desligamento']))) ?></dd>
            </dl>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex flex-wrap justify-content-end gap-2 pb-4">
            <a href="<?= e(url('admin/arquivo-morto/' . (int) $arquivo['id'])) ?>" class="btn btn-ad-outline">Cancelar</a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-rotate-left me-1"></i>Confirmar restauração
            </button>
        </div>
    </form>
</div>
