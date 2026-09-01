<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-people-group me-2 text-primary"></i>Célula: <?= esc($celula['nome']) ?></h4>
    <a href="<?= site_url('celulas') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold"><?= esc($celula['nome']) ?></h5>
                <?= badge_status($celula['ativo']) ?>
                <ul class="list-unstyled mt-3 mb-0">
                    <li class="mb-2"><i class="fa-solid fa-user-tie me-2 text-muted"></i><strong>Líder:</strong> <?= esc($lider['nome'] ?? '-') ?></li>
                    <li class="mb-2"><i class="fa-solid fa-house me-2 text-muted"></i><strong>Anfitrião:</strong> <?= esc($anfitriao['nome'] ?? '-') ?></li>
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-muted"></i><?= esc($celula['endereco'] ?: '-') ?></li>
                    <li class="mb-0"><i class="fa-solid fa-clock me-2 text-muted"></i><?= esc(($celula['dia_semana'] ?: '-') . ($celula['horario'] ? ' ' . $celula['horario'] : '')) ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-people-group me-2"></i>Membros (<?= count($membros) ?>)</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Nome</th><th>Telefone</th><th>Status</th><th class="text-end">Ações</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($membros)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Nenhum membro vinculado a esta célula.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($membros as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($m['nome']) ?></td>
                                <td><?= esc($m['telefone'] ?: '-') ?></td>
                                <td><?= esc($m['status_membro']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-secondary" href="<?= site_url('membros/ficha/' . $m['id']) ?>" title="Ficha"><i class="fa-solid fa-id-card"></i></a>
                                        <?php if (tem_permissao('celulas', 'editar')): ?>
                                            <form method="post" action="<?= site_url('celulas/removerMembro/' . $celula['id'] . '/' . $m['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-outline-danger" data-confirmar="Remover <?= esc($m['nome']) ?> da célula?" title="Remover"><i class="fa-solid fa-user-minus"></i></button>
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
        </div>

        <?php if (tem_permissao('celulas', 'editar')): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Vincular Membro</div>
            <div class="card-body">
                <form method="post" action="<?= site_url('celulas/vincularMembro/' . $celula['id']) ?>" class="row g-2">
                    <?= csrf_field() ?>
                    <div class="col-md-8">
                        <select name="membro_id" class="form-select" required>
                            <option value="">Selecione um membro...</option>
                            <?php foreach ($membrosDisponiveis as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= esc($m['nome']) ?> (<?= esc($m['congregacao'] ?? 'Sem congregação') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100"><i class="fa-solid fa-link me-1"></i>Vincular</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
