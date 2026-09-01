<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-briefcase me-2 text-primary"></i>Cargos de Obreiros</h4>
    <?php if (tem_permissao('cargos', 'cadastrar')): ?>
        <a href="<?= site_url('cargos/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Cargo</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome ou descrição..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th class="text-center">Ordem</th>
                    <th class="text-center">Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($cargos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum cargo cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($cargos as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($c['nome']) ?></td>
                    <td><?= esc($c['descricao'] ?? '-') ?></td>
                    <td class="text-center"><?= (int) $c['ordem'] ?></td>
                    <td class="text-center"><?= badge_status($c['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('cargos', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('cargos/editar/' . $c['id']) ?>"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('cargos', 'excluir')): ?>
                                <form method="post" action="<?= site_url('cargos/excluir/' . $c['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o cargo <?= esc($c['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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

<?php if ($total > $porPagina): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php for ($i = 1; $i <= (int) ceil($total / $porPagina); $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                <a class="page-link" href="<?= site_url('cargos') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>