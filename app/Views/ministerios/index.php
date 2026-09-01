<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-hands-praying me-2 text-primary"></i>Ministérios</h4>
    <?php if (tem_permissao('ministerios', 'cadastrar')): ?>
        <a href="<?= site_url('ministerios/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Ministério</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>Descrição</th><th>Líder</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($ministerios)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum ministério cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($ministerios as $m): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td class="fw-semibold"><?= esc($m['nome']) ?></td>
                    <td><?= esc($m['descricao'] ?: '-') ?></td>
                    <td><?= esc($m['lider'] ?? '-') ?></td>
                    <td><?= badge_status($m['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('ministerios', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('ministerios/editar/' . $m['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('ministerios', 'excluir')): ?>
                                <form method="post" action="<?= site_url('ministerios/excluir/' . $m['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o ministério <?= esc($m['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
