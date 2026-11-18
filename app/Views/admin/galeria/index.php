<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= e(url('galeria')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver no site
        </a>
        <?php if ($podeCriar): ?>
            <a href="<?= e(url('admin/galeria/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Nova fotografia
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 admin-card-titulo mb-0">Fotografias cadastradas</h2>
            <span class="badge ad-badge"><?= count($fotos) ?> foto(s)</span>
        </div>

        <?php if ($fotos === []): ?>
            <div class="admin-vazio text-center py-5">
                <i class="fas fa-images fa-2x mb-3 d-block"></i>
                Nenhuma fotografia cadastrada.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fotografia</th>
                            <th>Evento</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fotos as $foto): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($foto['imagem'])): ?>
                                            <img src="<?= e(url('uploads/' . $foto['imagem'])) ?>" alt="" class="admin-evento-thumb">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= e($foto['titulo'] !== '' ? $foto['titulo'] : 'Sem título') ?></strong>
                                            <?php if (!empty($foto['descricao'])): ?>
                                                <div class="small text-muted"><?= e($foto['descricao']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($foto['evento_titulo'])): ?>
                                        <span class="small"><?= e($foto['evento_titulo']) ?></span>
                                    <?php else: ?>
                                        <span class="small text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="small"><?= (int) $foto['ordem'] ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= (int) $foto['ativa'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= (int) $foto['ativa'] === 1 ? 'Ativa' : 'Oculta' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ($podeEditar): ?>
                                            <a href="<?= e(url('admin/galeria/' . (int) $foto['id'] . '/editar')) ?>"
                                               class="btn btn-sm btn-ad-outline" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($podeExcluir): ?>
                                            <form method="post" action="<?= e(url('admin/galeria/' . (int) $foto['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir esta fotografia?');">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-ad-outline text-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
