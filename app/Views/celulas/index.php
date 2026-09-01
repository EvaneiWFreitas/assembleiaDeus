<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-house-chimney-user me-2 text-primary"></i>Células</h4>
    <?php if (tem_permissao('celulas', 'cadastrar')): ?>
        <a href="<?= site_url('celulas/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nova Célula</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>Líder</th><th>Endereço</th><th>Dia / Hora</th><th>Membros</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($celulas)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhuma célula cadastrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($celulas as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td class="fw-semibold"><?= esc($c['nome']) ?></td>
                    <td><?= esc($c['lider'] ?? '-') ?></td>
                    <td><?= esc(($c['dia_semana'] ?: '-') . ($c['horario'] ? ' ' . $c['horario'] : '')) ?></td>
                    <td><span class="badge bg-info-subtle text-info"><?= (int) ($c['total_participantes'] ?? 0) ?></span></td>
                    <td><?= badge_status($c['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-secondary" href="<?= site_url('celulas/membros/' . $c['id']) ?>" title="Membros"><i class="fa-solid fa-people-group"></i></a>
                            <?php if (tem_permissao('celulas', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('celulas/editar/' . $c['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('celulas', 'excluir')): ?>
                                <form method="post" action="<?= site_url('celulas/excluir/' . $c['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir a célula <?= esc($c['nome']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
