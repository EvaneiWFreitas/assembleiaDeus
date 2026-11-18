<?php $colunasTotal = 6 + (can($permissoes['editar']) || can($permissoes['excluir']) ? 1 : 0); ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1">Usuários</h1>
        <p class="text-muted small mb-0">Contas com acesso ao painel administrativo.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary">Total: <?= count($usuarios) ?></span>
        <?php if (can($permissoes['criar'])): ?>
            <a href="<?= e(url('admin/usuarios/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Adicionar usuário
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table admin-tabela mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Congregação</th>
                    <th>Último acesso</th>
                    <th>Status</th>
                    <?php if (can($permissoes['editar']) || can($permissoes['excluir'])): ?>
                        <th class="text-end">Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios === []): ?>
                    <tr>
                        <td colspan="<?= $colunasTotal ?>" class="admin-vazio text-center py-4">Nenhum usuário cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($usuario['nome']) ?></div>
                                <?php if ((int) $usuario['must_change_password'] === 1): ?>
                                    <span class="badge bg-warning text-dark">senha provisória</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($usuario['email']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($usuario['perfil_nome']) ?></span>
                                <small class="text-muted">nível <?= (int) $usuario['perfil_nivel'] ?></small>
                            </td>
                            <td><?= e($usuario['congregacao_nome'] ?: '—') ?></td>
                            <td class="text-nowrap small">
                                <?= e($usuario['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : '—') ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= (int) $usuario['ativo'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int) $usuario['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <?php if (can($permissoes['editar']) || can($permissoes['excluir'])): ?>
                                <td>
                                    <div class="admin-acoes-tabela">
                                        <?php if (can($permissoes['editar'])): ?>
                                            <a class="btn btn-sm btn-ad-outline"
                                               href="<?= e(url('admin/usuarios/' . (int) $usuario['id'] . '/editar')) ?>"
                                               title="Editar usuário">
                                                <i class="fas fa-pen"></i><span class="d-lg-none ms-1">Editar</span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (can($permissoes['excluir']) && (int) $usuario['id'] !== $usuario_logado_id): ?>
                                            <form method="post"
                                                  action="<?= e(url('admin/usuarios/' . (int) $usuario['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir este usuário?');">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir usuário">
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

<p class="text-muted small mt-3 mb-0">
    <i class="fas fa-circle-info me-1"></i>
    Ao excluir uma conta, os registros vinculados a ela permanecem no sistema.
</p>
