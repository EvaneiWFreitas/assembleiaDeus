<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-gear me-2 text-primary"></i>Usuários</h4>
    <?php if (tem_permissao('usuarios', 'cadastrar')): ?>
        <a href="<?= site_url('usuarios/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Usuário</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome ou e-mail..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Nome</th><th>E-mail</th><th>Perfil</th>
                    <th>Telefone</th><th>Último Login</th><th>Situação</th><th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum usuário encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td class="fw-semibold"><?= esc($u['nome']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td><span class="badge bg-primary-subtle text-primary"><?= esc($u['role']) ?></span></td>
                    <td><?= esc($u['telefone'] ?: '-') ?></td>
                    <td><?= formatar_data_hora($u['ultimo_login']) ?></td>
                    <td>
                        <?= badge_status($u['ativo']) ?>
                        <?php if ($u['bloqueado']): ?><span class="badge bg-danger">Bloqueado</span><?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('usuarios', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('usuarios/editar/' . $u['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <form method="post" action="<?= site_url('usuarios/bloquear/' . $u['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-secondary" title="<?= $u['bloqueado'] ? 'Desbloquear' : 'Bloquear' ?>">
                                        <i class="fa-solid <?= $u['bloqueado'] ? 'fa-lock-open' : 'fa-lock' ?>"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if (tem_permissao('usuarios', 'excluir')): ?>
                                <form method="post" action="<?= site_url('usuarios/excluir/' . $u['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o usuário <?= esc($u['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($total ?? 0) > $porPagina): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php for ($i = 1; $i <= (int) ceil($total / $porPagina); $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                <a class="page-link" href="<?= site_url('usuarios') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
