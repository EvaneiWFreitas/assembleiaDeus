<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-money-check-dollar me-2 text-success"></i>Receitas</h4>
    <?php if (tem_permissao('financeiro', 'cadastrar')): ?>
        <a href="<?= site_url('receitas/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nova Receita</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Descrição</th><th>Categoria</th><th>Data</th><th>Forma Pgto</th><th class="text-end">Valor</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($receitas)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhuma receita registrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($receitas as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td class="fw-semibold"><?= esc($r['descricao']) ?></td>
                    <td><?= esc($r['categoria'] ?? '—') ?></td>
                    <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                    <td><?= esc($r['forma_pagamento'] ?? '—') ?></td>
                    <td class="text-end fw-bold text-success"><?= formatar_moeda($r['valor']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('financeiro', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('receitas/editar/' . $r['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('financeiro', 'excluir')): ?>
                                <form method="post" action="<?= site_url('receitas/excluir/' . $r['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir esta receita?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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

<?= $this->endSection() ?>