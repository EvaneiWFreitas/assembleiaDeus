<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($titulo) ?></h1>
        <p class="text-muted small mb-0"><?= e($descricao) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary">Total: <?= count($itens) ?></span>
        <?php if ($podeArquivar): ?>
            <a href="<?= e(url('admin/arquivo-morto/arquivar')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-box-archive me-1"></i>Arquivar obreiro
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <form method="get" action="<?= e(url('admin/arquivo-morto')) ?>" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="filtro_busca" class="form-label small mb-1">Busca</label>
                <input type="text" class="form-control form-control-sm" id="filtro_busca" name="busca"
                       value="<?= e($filtros['busca'] ?? '') ?>"
                       placeholder="Nome, código, CPF ou motivo">
            </div>
            <div class="col-md-3">
                <label for="filtro_estado" class="form-label small mb-1">Estado</label>
                <select class="form-select form-select-sm" id="filtro_estado" name="estado">
                    <option value="">Todos</option>
                    <option value="ARQUIVADO" <?= ($filtros['estado'] ?? '') === 'ARQUIVADO' ? 'selected' : '' ?>>Arquivado</option>
                    <option value="RESTAURADO" <?= ($filtros['estado'] ?? '') === 'RESTAURADO' ? 'selected' : '' ?>>Restaurado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro_congregacao_id" class="form-label small mb-1">Congregação</label>
                <select class="form-select form-select-sm" id="filtro_congregacao_id" name="congregacao_id">
                    <option value="">Todas</option>
                    <?php foreach ($congregacoes as $valor => $rotulo): ?>
                        <option value="<?= e($valor) ?>"
                                <?= (string) ($filtros['congregacao_id'] ?? '') === (string) $valor ? 'selected' : '' ?>>
                            <?= e($rotulo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-ad-primary">
                    <i class="fas fa-magnifying-glass me-1"></i>Filtrar
                </button>
                <a href="<?= e(url('admin/arquivo-morto')) ?>" class="btn btn-sm btn-ad-outline ms-1">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table admin-tabela mb-0 align-middle">
            <thead>
                <tr>
                    <th>Obreiro</th>
                    <th>Cargo</th>
                    <th>Congregação</th>
                    <th>Motivo</th>
                    <th>Data</th>
                    <th>Situação</th>
                    <th>Responsável</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($itens === []): ?>
                    <tr>
                        <td colspan="8" class="admin-vazio text-center py-4">
                            Nenhum registro no arquivo morto.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <?php $restaurado = !empty($item['restaurado_em']); ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($item['nome_completo']) ?></div>
                                <small class="text-muted"><?= e($item['codigo']) ?> · <?= e(formatarCpf($item['cpf'])) ?></small>
                            </td>
                            <td><span class="small"><?= e($item['cargo_nome'] ?: '—') ?></span></td>
                            <td><span class="small"><?= e($item['congregacao_nome'] ?: '—') ?></span></td>
                            <td><span class="small"><?= e(mb_strimwidth((string) ($item['motivo'] ?? ''), 0, 70, '...')) ?></span></td>
                            <td><span class="text-nowrap small"><?= e(date('d/m/Y', strtotime((string) $item['data_desligamento']))) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $restaurado ? 'success' : 'secondary' ?>">
                                    <?= $restaurado ? 'Restaurado' : 'Arquivado' ?>
                                </span>
                            </td>
                            <td><span class="small"><?= e($item['responsavel_nome'] ?: '—') ?></span></td>
                            <td>
                                <div class="admin-acoes-tabela">
                                    <a class="btn btn-sm btn-ad-outline"
                                       href="<?= e(url('admin/arquivo-morto/' . (int) $item['id'])) ?>"
                                       title="Ver ficha histórica">
                                        <i class="fas fa-eye"></i><span class="d-lg-none ms-1">Ver</span>
                                    </a>
                                    <?php if ($podeRestaurar && !$restaurado): ?>
                                        <a class="btn btn-sm btn-outline-success"
                                           href="<?= e(url('admin/arquivo-morto/' . (int) $item['id'] . '/restaurar')) ?>"
                                           title="Restaurar obreiro">
                                            <i class="fas fa-rotate-left"></i><span class="d-lg-none ms-1">Restaurar</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<p class="text-muted small mt-3 mb-0">
    <i class="fas fa-circle-info me-1"></i>
    O arquivamento preserva os dados originais. A restauração é restrita ao Superadministrador.
</p>
