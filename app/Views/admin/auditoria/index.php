<div class="mb-4">
    <h1 class="admin-titulo-pagina mb-1">Auditoria</h1>
    <p class="text-muted small mb-0">Registros de acesso e alterações sensíveis.</p>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Últimos acessos</h2>
                <div class="table-responsive">
                    <table class="table admin-tabela mb-0 small">
                        <thead>
                            <tr><th>Ação</th><th>Usuário</th><th>Detalhes</th><th>Data</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($acessos === []): ?>
                                <tr><td colspan="4" class="admin-vazio text-center py-3">Sem registros.</td></tr>
                            <?php else: ?>
                                <?php foreach ($acessos as $acesso): ?>
                                    <tr>
                                        <td><span class="badge bg-light border"><?= e(str_replace('_', ' ', $acesso['acao'])) ?></span></td>
                                        <td><?= e($acesso['usuario_nome'] ?: 'Sistema') ?></td>
                                        <td><?= e($acesso['detalhes'] ?: '—') ?></td>
                                        <td class="text-nowrap"><?= e(date('d/m H:i', strtotime($acesso['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Últimas alterações</h2>
                <div class="table-responsive">
                    <table class="table admin-tabela mb-0 small">
                        <thead>
                            <tr><th>Ação</th><th>Módulo</th><th>Registro</th><th>Data</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($alteracoes === []): ?>
                                <tr><td colspan="4" class="admin-vazio text-center py-3">Sem registros.</td></tr>
                            <?php else: ?>
                                <?php foreach ($alteracoes as $alteracao): ?>
                                    <tr>
                                        <td><span class="badge bg-light border"><?= e($alteracao['acao']) ?></span></td>
                                        <td><?= e($alteracao['modulo']) ?></td>
                                        <td>
                                            <?= e($alteracao['tabela'] ?: '—') ?>
                                            <?php if (!empty($alteracao['registro_id'])): ?>
                                                #<?= e($alteracao['registro_id']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap"><?= e(date('d/m H:i', strtotime($alteracao['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
