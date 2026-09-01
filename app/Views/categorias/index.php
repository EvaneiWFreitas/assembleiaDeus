<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-tags me-2 text-warning"></i>Categorias</h4>
    <?php if (tem_permissao('financeiro', 'cadastrar')): ?>
        <a href="<?= site_url('categorias/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nova Categoria</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>Tipo</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($categorias)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td class="fw-semibold"><?= esc($cat['nome']) ?></td>
                    <td>
                        <?php if ($cat['tipo'] === 'entrada'): ?>
                            <span class="badge bg-success-subtle text-success">Entrada</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Saída</span>
                        <?php endif; ?>
                    </td>
                    <td><?= badge_status($cat['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('financeiro', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('categorias/editar/' . $cat['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('financeiro', 'excluir')): ?>
                                <form method="post" action="<?= site_url('categorias/excluir/' . $cat['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir a categoria <?= esc($cat['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
