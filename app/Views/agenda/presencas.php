<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-check me-2 text-primary"></i>Presenças</h4>
        <div class="text-muted small">
            <i class="fa-solid fa-calendar-days me-1"></i><?= esc($evento['titulo']) ?>
            <span class="mx-1">·</span> <?= formatar_data($evento['data_inicio']) ?>
            <span class="mx-1">·</span><span class="badge bg-info-subtle text-info"><?= $total ?> confirmada<?= $total !== 1 ? 's' : '' ?></span>
        </div>
    </div>
    <a href="<?= site_url('agenda') ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><?= esc($e) ?></div>
<?php endforeach ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>Lista de Confirmações
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Pessoa</th><th>Telefone</th><th>Confirmado em</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($presencas)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma confirmação de presença até o momento.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($presencas as $p): ?>
                        <?php $fotoMembro = $p['membro_foto'] && is_file(ROOTPATH . 'public/uploads/membros/' . $p['membro_foto']) ? $p['membro_foto'] : null; ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($fotoMembro): ?>
                                        <img src="<?= base_url('uploads/membros/' . $fotoMembro) ?>" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;min-width:36px;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-grid place-items-center" style="width:36px;height:36px;min-width:36px;font-size:.8rem;font-weight:700;">
                                            <?= esc(mb_substr($p['nome'] ?? '?', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="fw-semibold"><?= esc($p['nome']) ?></span>
                                    <?php if (!empty($p['membro_id'])): ?>
                                        <span class="badge bg-primary-subtle text-primary">Membro</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= esc($p['telefone'] ?: '-') ?></td>
                            <td><?= formatar_data_hora($p['confirmado_em'] ?: $p['created_at']) ?></td>
                            <td class="text-end">
                                <?php if (tem_permissao('agenda', 'excluir')): ?>
                                    <form method="post" action="<?= site_url('agenda/presencas/remover/' . $p['id']) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-sm" data-confirmar="Remover a presença de <?= esc($p['nome']) ?>?" title="Remover"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?php if (tem_permissao('agenda', 'editar')): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold">
                <i class="fa-solid fa-plus me-2 text-success"></i>Registrar Presença
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('agenda/presencas/' . $evento['id'] . '/adicionar') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Membro cadastrado</label>
                        <select name="membro_id" class="form-select">
                            <option value="">Nenhum (digite o nome)</option>
                            <?php foreach ($membros as $id => $nome): ?>
                                <option value="<?= $id ?>"><?= esc($nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" required minlength="2" value="<?= old('nome') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control mascara-telefone" value="<?= old('telefone') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmado em</label>
                        <input type="datetime-local" name="confirmado_em" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <button class="btn btn-success w-100"><i class="fa-solid fa-check me-1"></i>Registrar</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>