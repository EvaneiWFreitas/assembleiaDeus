<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('#horarios')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver seção
    </a>
</div>

<div class="card admin-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-pen-nib me-2"></i>Textos da seção</h2>
        <form method="post" action="<?= e(url('admin/horarios/textos')) ?>" class="admin-form">
            <?= App\Core\Csrf::field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="campo_eyebrow" class="form-label">Subtítulo</label>
                    <input type="text" class="form-control" id="campo_eyebrow" name="cultos_eyebrow"
                           value="<?= e($textos['cultos_eyebrow']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="campo_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_titulo" name="cultos_titulo"
                           value="<?= e($textos['cultos_titulo']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="campo_vazio" class="form-label">Mensagem quando não houver horários</label>
                    <input type="text" class="form-control" id="campo_vazio" name="cultos_vazio"
                           value="<?= e($textos['cultos_vazio']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="campo_botao" class="form-label">Texto do botão</label>
                    <input type="text" class="form-control" id="campo_botao" name="cultos_botao"
                           value="<?= e($textos['cultos_botao']) ?>">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-sm btn-ad-primary">
                    <i class="fas fa-check me-1"></i>Salvar textos
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 admin-card-titulo mb-0">Horários cadastrados</h2>
            <a href="<?= e(url('admin/horarios/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Novo horário
            </a>
        </div>

        <?php if ($horarios === []): ?>
            <div class="admin-vazio text-center py-5">
                <i class="fas fa-clock fa-2x mb-3 d-block"></i>
                Nenhum horário cadastrado. A seção usa a mensagem configurada acima.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>Horário</th>
                            <th>Local</th>
                            <th>Culto / Atividade</th>
                            <th>Duração</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $horario): ?>
                            <?php
                            $valorLocal = $horario['local_tipo'] . ':' . $horario['local_id'];
                            $rotuloLocal = $rotulosLocais[$valorLocal] ?? $valorLocal;
                            ?>
                            <tr>
                                <td><strong><?= e($dias[$horario['dia_semana']] ?? $horario['dia_semana']) ?></strong></td>
                                <td><?= e(substr((string) $horario['horario'], 0, 5)) ?></td>
                                <td><?= e($rotuloLocal) ?></td>
                                <td><?= e($horario['descricao'] ?? '-') ?></td>
                                <td><?= $horario['duracao_minutos'] !== null ? (int) $horario['duracao_minutos'] . ' min' : '-' ?></td>
                                <td>
                                    <span class="badge <?= (int) $horario['ativo'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= (int) $horario['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= e(url('admin/horarios/' . (int) $horario['id'] . '/editar')) ?>"
                                           class="btn btn-sm btn-ad-outline" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form method="post" action="<?= e(url('admin/horarios/' . (int) $horario['id'] . '/excluir')) ?>"
                                              onsubmit="return confirm('Excluir este horário?');">
                                            <?= App\Core\Csrf::field() ?>
                                            <button type="submit" class="btn btn-sm btn-ad-outline text-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
