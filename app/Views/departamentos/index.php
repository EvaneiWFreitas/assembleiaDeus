<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-sitemap me-2 text-primary"></i>Departamentos</h4>
    <?php if (tem_permissao('departamentos', 'cadastrar')): ?>
        <a href="<?= site_url('departamentos/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Departamento</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>Descrição</th><th>Líder</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($departamentos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum departamento cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($departamentos as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td class="fw-semibold"><?= esc($d['nome']) ?></td>
                    <td><?= esc($d['descricao'] ?: '-') ?></td>
                    <td><?= esc($d['lider'] ?? '-') ?></td>
                    <td><?= badge_status($d['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('departamentos', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('departamentos/editar/' . $d['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('departamentos', 'excluir')): ?>
                                <form method="post" action="<?= site_url('departamentos/excluir/' . $d['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o departamento <?= esc($d['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
