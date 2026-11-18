<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= e(url('admin/eventos/textos')) ?>" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-pen-nib me-1"></i>Textos da agenda
        </a>
        <a href="<?= e(url('eventos')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver agenda
        </a>
        <?php if ($podeCriar): ?>
            <a href="<?= e(url('admin/eventos/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Novo evento
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 admin-card-titulo mb-0">Eventos cadastrados</h2>
            <span class="badge ad-badge"><?= count($eventos) ?> evento(s)</span>
        </div>

        <?php if ($eventos === []): ?>
            <div class="admin-vazio text-center py-5">
                <i class="fas fa-calendar-days fa-2x mb-3 d-block"></i>
                Nenhum evento cadastrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Tipo</th>
                            <th>Início</th>
                            <th>Local</th>
                            <th>Situação</th>
                            <th>Exibição</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eventos as $evento): ?>
                            <?php
                            $inicio = !empty($evento['data_inicio'])
                                ? date('d/m/Y H:i', strtotime((string) $evento['data_inicio']))
                                : '-';
                            $situacao = (string) $evento['situacao'];
                            $corSituacao = match ($situacao) {
                                'CONCLUIDO'     => 'text-bg-success',
                                'EM_ANDAMENTO'  => 'text-bg-primary',
                                'CANCELADO'     => 'text-bg-danger',
                                default         => 'text-bg-secondary',
                            };
                            $rotuloSituacao = match ($situacao) {
                                'CONCLUIDO'     => 'Concluído',
                                'EM_ANDAMENTO'  => 'Em andamento',
                                'CANCELADO'     => 'Cancelado',
                                default         => 'Planejado',
                            };
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($evento['foto'])): ?>
                                            <img src="<?= e(url('uploads/' . $evento['foto'])) ?>" alt="" class="admin-evento-thumb">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= e($evento['titulo']) ?></strong>
                                            <?php if (!empty($evento['responsavel_nome'])): ?>
                                                <div class="small text-muted">Resp.: <?= e($evento['responsavel_nome']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($evento['tipo_nome'] ?? '-') ?></td>
                                <td><?= e($inicio) ?></td>
                                <td>
                                    <?= e($evento['local_evento'] ?: '-') ?>
                                    <?php if (!empty($evento['congregacao_nome'])): ?>
                                        <div class="small text-muted"><?= e($evento['congregacao_nome']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $corSituacao ?>"><?= e($rotuloSituacao) ?></span></td>
                                <td>
                                    <span class="badge <?= (int) $evento['publicar_site'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= (int) $evento['publicar_site'] === 1 ? 'Publicado' : 'Oculto' ?>
                                    </span>
                                    <?php if ((int) $evento['destaque'] === 1): ?>
                                        <span class="badge text-bg-warning">Destaque</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= e(url('eventos/' . (int) $evento['id'])) ?>" target="_blank" rel="noopener noreferrer"
                                           class="btn btn-sm btn-ad-outline" title="Ver no site">
                                            <i class="fas fa-arrow-up-right-from-square"></i>
                                        </a>
                                        <?php if ($podeEditar): ?>
                                            <a href="<?= e(url('admin/eventos/' . (int) $evento['id'] . '/editar')) ?>"
                                               class="btn btn-sm btn-ad-outline" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($podeExcluir): ?>
                                            <form method="post" action="<?= e(url('admin/eventos/' . (int) $evento['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir este evento?');">
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
