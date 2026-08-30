<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-tie me-2 text-primary"></i>Pastores & Obreiros</h4>
    <?php if (tem_permissao('obreiros', 'cadastrar')): ?>
        <a href="<?= site_url('obreiros/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Obreiro</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="cargo" class="form-select">
            <option value="">Todos os cargos</option>
            <?php foreach ($cargos as $c): ?>
                <option value="<?= esc($c) ?>" <?= $cargoFiltro === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nome</th><th>Cargo</th><th>Registro</th><th>Consagração</th><th>Congregação</th><th>Telefone</th><th>Situação</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($obreiros)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum obreiro cadastrado. Cadastre o membro primeiro e depois o vincule como obreiro.</td></tr>
            <?php endif; ?>
            <?php foreach ($obreiros as $o): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($o['nome']) ?></td>
                    <td><span class="badge bg-primary-subtle text-primary"><?= esc($o['cargo']) ?></span></td>
                    <td><?= esc($o['numero_registro'] ?: '-') ?></td>
                    <td><?= formatar_data($o['data_conexao']) ?></td>
                    <td><?= esc($o['congregacao'] ?? '-') ?></td>
                    <td><?= esc($o['telefone'] ?: '-') ?></td>
                    <td><?= badge_status($o['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('obreiros', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('obreiros/editar/' . $o['id']) ?>"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('obreiros', 'excluir')): ?>
                                <form method="post" action="<?= site_url('obreiros/excluir/' . $o['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o vínculo de obreiro de <?= esc($o['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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
                <a class="page-link" href="<?= site_url('obreiros') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?><?= $cargoFiltro ? '&cargo=' . urlencode($cargoFiltro) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
