<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <span class="badge ad-badge">
        <?= (int) $naoLidos ?> não lida(s) · <?= count($contatos) ?> mensagem(ns)
    </span>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <?php if ($contatos === []): ?>
            <div class="admin-vazio text-center py-5">
                <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                Nenhuma mensagem recebida ainda.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Remetente</th>
                            <th>Assunto</th>
                            <th>Recebida em</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contatos as $contato): ?>
                            <?php
                            $lido = (int) ($contato['lido'] ?? 0) === 1;
                            $respondido = (int) ($contato['respondido'] ?? 0) === 1;
                            ?>
                            <tr class="<?= $lido ? '' : 'table-light' ?>">
                                <td>
                                    <a href="<?= e(url('admin/contatos/' . (int) $contato['id'])) ?>" class="fw-semibold text-decoration-none">
                                        <?= e((string) ($contato['nome'] ?? '')) ?>
                                    </a>
                                    <div class="small text-muted"><?= e((string) ($contato['email'] ?? '')) ?></div>
                                </td>
                                <td>
                                    <span class="small"><?= e((string) ($contato['assunto'] ?? '')) ?></span>
                                </td>
                                <td>
                                    <span class="small text-nowrap"><?= date('d/m/Y H:i', strtotime((string) $contato['created_at'])) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge <?= $lido ? 'text-bg-secondary' : 'text-bg-primary' ?>">
                                            <?= $lido ? 'Lida' : 'Nova' ?>
                                        </span>
                                        <span class="badge <?= $respondido ? 'text-bg-success' : 'text-bg-warning' ?>">
                                            <?= $respondido ? 'Respondida' : 'Pendente' ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= e(url('admin/contatos/' . (int) $contato['id'])) ?>"
                                           class="btn btn-sm btn-ad-outline" title="Ver mensagem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!$lido): ?>
                                            <form method="post" action="<?= e(url('admin/contatos/' . (int) $contato['id'] . '/lido')) ?>">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-ad-outline" title="Marcar como lida">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($podeResponder && !$respondido): ?>
                                            <form method="post" action="<?= e(url('admin/contatos/' . (int) $contato['id'] . '/respondido')) ?>">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-ad-outline" title="Marcar como respondida">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($podeExcluir): ?>
                                            <form method="post" action="<?= e(url('admin/contatos/' . (int) $contato['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir esta mensagem?');">
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
