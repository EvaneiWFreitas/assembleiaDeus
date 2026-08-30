<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-people-group me-2 text-primary"></i>Membros</h4>
    <?php if (tem_permissao('membros', 'cadastrar')): ?>
        <a href="<?= site_url('membros/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Membro</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome, CPF ou e-mail..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <?php foreach (['Ativo', 'Inativo', 'Transferido', 'Desligado', 'Falecido'] as $s): ?>
                <option value="<?= $s ?>" <?= $statusFiltro === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="congregacao" class="form-select">
            <option value="">Todas as congregações</option>
            <?php foreach ($congregacoes as $id => $nome): ?>
                <option value="<?= $id ?>" <?= $congregacaoFiltro == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>CPF</th><th>Nascimento</th><th>Telefone</th><th>Congregação</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($membros)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum membro encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($membros as $m): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td class="fw-semibold"><?= esc($m['nome']) ?></td>
                    <td><?= esc($m['cpf'] ?: '-') ?></td>
                    <td><?= formatar_data($m['data_nascimento']) ?></td>
                    <td><?= esc($m['telefone'] ?: '-') ?></td>
                    <td><?= esc($m['congregacao'] ?? '-') ?></td>
                    <td>
                        <?php $cores = ['Ativo' => 'success', 'Inativo' => 'secondary', 'Transferido' => 'warning', 'Desligado' => 'danger', 'Falecido' => 'dark']; ?>
                        <span class="badge bg-<?= $cores[$m['status']] ?? 'secondary' ?>-subtle text-<?= $cores[$m['status']] ?? 'secondary' ?>"><?= esc($m['status']) ?></span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-secondary" href="<?= site_url('membros/ficha/' . $m['id']) ?>" title="Ficha"><i class="fa-solid fa-id-card"></i></a>
                            <?php if (tem_permissao('membros', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('membros/editar/' . $m['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('membros', 'excluir')): ?>
                                <form method="post" action="<?= site_url('membros/excluir/' . $m['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o membro <?= esc($m['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                <a class="page-link" href="<?= site_url('membros') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?><?= $statusFiltro ? '&status=' . urlencode($statusFiltro) : '' ?><?= $congregacaoFiltro ? '&congregacao=' . $congregacaoFiltro : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
