<?php
$colunasTotal = count($colunas) + (can($permissoes['editar']) || can($permissoes['excluir']) ? 1 : 0);
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($titulo) ?></h1>
        <p class="text-muted small mb-0"><?= e($descricao) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary">Total: <?= count($itens) ?></span>
        <?php if (can($permissoes['criar'])): ?>
            <a href="<?= e(url($rota . '/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Novo cadastro
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($filtrosFormulario['campos'])): ?>
    <div class="card admin-card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="get" action="<?= e($filtrosFormulario['acao'] ?? '') ?>" class="row g-2 align-items-end">
                <?php foreach ($filtrosFormulario['campos'] as $filtro): ?>
                    <div class="<?= e($filtro['col'] ?? 'col-md-3') ?>">
                        <label for="filtro_<?= e($filtro['name']) ?>" class="form-label small mb-1">
                            <?= e($filtro['label']) ?>
                        </label>
                        <?php if (($filtro['type'] ?? 'text') === 'select'): ?>
                            <select class="form-select form-select-sm" id="filtro_<?= e($filtro['name']) ?>"
                                    name="<?= e($filtro['name']) ?>">
                                <option value="">Todos</option>
                                <?php foreach (($filtro['options'] ?? []) as $valor => $rotulo): ?>
                                    <option value="<?= e($valor) ?>"
                                            <?= (string) ($filtro['value'] ?? '') === (string) $valor ? 'selected' : '' ?>>
                                        <?= e($rotulo) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?= e($filtro['type'] ?? 'text') ?>" class="form-control form-control-sm"
                                   id="filtro_<?= e($filtro['name']) ?>" name="<?= e($filtro['name']) ?>"
                                   value="<?= e($filtro['value'] ?? '') ?>"
                                   placeholder="<?= e($filtro['placeholder'] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-ad-primary">
                        <i class="fas fa-magnifying-glass me-1"></i>Filtrar
                    </button>
                    <a href="<?= e($filtrosFormulario['acao'] ?? '') ?>" class="btn btn-sm btn-ad-outline ms-1">Limpar</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card admin-card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table admin-tabela mb-0 align-middle">
            <thead>
                <tr>
                    <?php foreach ($colunas as $coluna): ?>
                        <th><?= e($coluna['label']) ?></th>
                    <?php endforeach; ?>
                    <?php if (can($permissoes['editar']) || can($permissoes['excluir'])): ?>
                        <th class="text-end">Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($itens === []): ?>
                    <tr>
                        <td colspan="<?= $colunasTotal ?>" class="admin-vazio text-center py-4">
                            Nenhum registro cadastrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <?php foreach ($colunas as $coluna): ?>
                                <?php $valor = $item[$coluna['key']] ?? null; ?>
                                <td>
                                    <?php if (($coluna['tipo'] ?? 'texto') === 'badge'): ?>
                                        <?php $cor = ($coluna['cores'][$valor] ?? $coluna['cor'] ?? 'secondary'); ?>
                                        <span class="badge bg-<?= e($cor) ?>"><?= e($coluna['rotulos'][$valor] ?? $valor) ?></span>
                                    <?php elseif (($coluna['tipo'] ?? 'texto') === 'cnpj'): ?>
                                        <span class="text-nowrap"><?= e(formatarCnpj($valor)) ?></span>
                                    <?php elseif (($coluna['tipo'] ?? 'texto') === 'cpf'): ?>
                                        <span class="text-nowrap"><?= e(formatarCpf($valor)) ?></span>
                                    <?php elseif (($coluna['tipo'] ?? 'texto') === 'data'): ?>
                                        <span class="text-nowrap"><?= e(empty($valor) ? '—' : date('d/m/Y', strtotime((string) $valor))) ?></span>
                                    <?php elseif (($coluna['tipo'] ?? 'texto') === 'principal'): ?>
                                        <div class="fw-semibold"><?= e($valor ?: '—') ?></div>
                                        <?php if (!empty($coluna['subtitulo']) && !empty($item[$coluna['subtitulo']])): ?>
                                            <small class="text-muted"><?= e($item[$coluna['subtitulo']]) ?></small>
                                        <?php endif; ?>
                                    <?php elseif (($coluna['tipo'] ?? 'texto') === 'contador'): ?>
                                        <span class="badge bg-light text-dark border"><?= (int) $valor ?></span>
                                    <?php else: ?>
                                        <span class="small"><?= e($valor ?: '—') ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <?php if (can($permissoes['editar']) || can($permissoes['excluir'])): ?>
                                <td>
                                    <div class="admin-acoes-tabela">
                                        <?php if (can($permissoes['editar'])): ?>
                                            <a class="btn btn-sm btn-ad-outline"
                                               href="<?= e(url($rota . '/' . (int) $item['id'] . '/editar')) ?>"
                                               title="Editar">
                                                <i class="fas fa-pen"></i><span class="d-lg-none ms-1">Editar</span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can($permissoes['excluir'])): ?>
                                            <form method="post" action="<?= e(url($rota . '/' . (int) $item['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir este registro?');">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i><span class="d-lg-none ms-1">Excluir</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($nota)): ?>
    <p class="text-muted small mt-3 mb-0">
        <i class="fas fa-circle-info me-1"></i><?= e($nota) ?>
    </p>
<?php endif; ?>
