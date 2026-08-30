<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Visitantes</h4>
    <?php if (tem_permissao('visitantes', 'cadastrar')): ?>
        <a href="<?= site_url('visitantes/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Visitante</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome ou telefone..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Todas as etapas</option>
            <?php foreach ($statusFunil as $s): ?>
                <option value="<?= esc($s) ?>" <?= $statusFiltro === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nome</th><th>Telefone</th><th>1ª Visita</th><th>Congregação</th><th>Responsável</th><th>Etapa</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($visitantes)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum visitante encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($visitantes as $v): ?>
                <?php $cores = ['Primeira visita' => 'primary', 'Contato realizado' => 'info', 'Nova visita' => 'info', 'Acompanhamento' => 'warning', 'Discipulado' => 'purple', 'Membro' => 'success', 'Desistiu' => 'secondary']; ?>
                <tr>
                    <td class="fw-semibold"><?= esc($v['nome']) ?></td>
                    <td><?= esc($v['telefone'] ?: '-') ?></td>
                    <td><?= formatar_data($v['data_primeira_visita']) ?></td>
                    <td><?= esc($v['congregacao'] ?? '-') ?></td>
                    <td><?= esc($v['acompanhamento_responsavel'] ?? '-') ?></td>
                    <td><span class="badge bg-<?= $cores[$v['status']] ?? 'secondary' ?>-subtle text-<?= $cores[$v['status']] ?? 'secondary' ?>"><?= esc($v['status']) ?></span></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('visitantes', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('visitantes/editar/' . $v['id']) ?>"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('visitantes', 'excluir')): ?>
                                <form method="post" action="<?= site_url('visitantes/excluir/' . $v['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o visitante <?= esc($v['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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
                <a class="page-link" href="<?= site_url('visitantes') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?><?= $statusFiltro ? '&status=' . urlencode($statusFiltro) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
