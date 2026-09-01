<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-hands-holding-circle me-2 text-primary"></i>Discipulado</h4>
    <?php if (tem_permissao('discipulados', 'cadastrar')): ?>
        <a href="<?= site_url('discipulados/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Discipulado</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Discípulo</th><th>Discipulador</th><th>Início</th><th>Conclusão</th><th>Encontros</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($discipulados)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum discipulado cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($discipulados as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td class="fw-semibold"><?= esc($d['discipulo'] ?? '-') ?></td>
                    <td><?= esc($d['discipulador'] ?? '-') ?></td>
                    <td><?= formatar_data($d['data_inicio'] ?? null) ?></td>
                    <td><?= formatar_data($d['data_conclusao'] ?? null) ?></td>
                    <td><span class="badge bg-secondary-subtle text-secondary"><?= (int) ($d['total_encontros'] ?? 0) ?></span></td>
                    <td>
                        <span class="badge <?= ['Em andamento' => 'bg-primary', 'Concluído' => 'bg-success', 'Cancelado' => 'bg-danger'][$d['status']] ?? 'bg-secondary' ?>"><?= esc($d['status']) ?></span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('discipulados', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('discipulados/editar/' . $d['id']) ?>" title="Editar / Encontros"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('discipulados', 'excluir')): ?>
                                <form method="post" action="<?= site_url('discipulados/excluir/' . $d['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o discipulado #<?= $d['id'] ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
