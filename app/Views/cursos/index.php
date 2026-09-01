<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i>Cursos</h4>
    <?php if (tem_permissao('cursos', 'cadastrar')): ?>
        <a href="<?= site_url('cursos/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Curso</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nome</th><th>Professor</th><th>Período</th><th>Vagas</th><th>Alunos</th><th>Aulas</th><th>Situação</th><th>Status</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($cursos)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Nenhum curso cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($cursos as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($c['nome']) ?></td>
                    <td><?= esc($c['professor'] ?? '-') ?></td>
                    <td>
                        <?= formatar_data($c['data_inicio'] ?? null) ?>
                        <?= ! empty($c['data_fim']) ? ' até ' . formatar_data($c['data_fim']) : '' ?>
                    </td>
                    <td><?= $c['vagas'] ?: '-' ?></td>
                    <td><span class="badge bg-info-subtle text-info"><?= (int) $c['total_alunos'] ?></span></td>
                    <td><span class="badge bg-secondary-subtle text-secondary"><?= (int) $c['total_aulas'] ?></span></td>
                    <td><?= badge_status($c['ativo']) ?></td>
                    <td><span class="badge <?= ['Planejado' => 'bg-warning text-dark', 'Em andamento' => 'bg-primary', 'Concluído' => 'bg-success', 'Cancelado' => 'bg-danger'][$c['status']] ?? 'bg-secondary' ?>"><?= esc($c['status']) ?></span></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('cursos', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('cursos/editar/' . $c['id']) ?>" title="Gerenciar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('cursos', 'excluir')): ?>
                                <form method="post" action="<?= site_url('cursos/excluir/' . $c['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o curso <?= esc($c['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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
