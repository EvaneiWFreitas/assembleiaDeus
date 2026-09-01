<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-hands-holding-circle me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <a href="<?= site_url('discipulados') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= isset($registro) ? site_url('discipulados/atualizar/' . $registro['id']) : site_url('discipulados/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label">Discípulo <span class="text-danger">*</span></label>
                <select name="discipulo_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['discipulo_id'] ?? old('discipulo_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Discipulador <span class="text-danger">*</span></label>
                <select name="discipulador_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['discipulador_id'] ?? old('discipulador_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Data de Início</label>
                <input type="date" name="data_inicio" class="form-control" value="<?= esc($registro['data_inicio'] ?? old('data_inicio') ?: date('Y-m-d')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Conclusão</label>
                <input type="date" name="data_conclusao" class="form-control" value="<?= esc($registro['data_conclusao'] ?? old('data_conclusao')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['Em andamento', 'Concluído', 'Cancelado'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($registro['status'] ?? old('status') ?: 'Em andamento') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label">Observações</label>
                <input type="text" name="observacoes" class="form-control" value="<?= esc($registro['observacoes'] ?? old('observacoes')) ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('discipulados') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($registro)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold"><i class="fa-solid fa-calendar-check me-2"></i>Encontros (<?= count($encontros ?? []) ?>)</div>
    <div class="card-body">
        <?php if (tem_permissao('discipulados', 'editar')): ?>
        <form method="post" action="<?= site_url('discipulados/encontro/' . $registro['id']) ?>" class="row g-2 mb-4">
            <?= csrf_field() ?>
            <div class="col-md-3"><input type="date" name="data" class="form-control form-control-sm" required></div>
            <div class="col-md-3"><input type="text" name="etapa" class="form-control form-control-sm" placeholder="Etapa / tema (opcional)"></div>
            <div class="col-md-4"><input type="text" name="observacoes" class="form-control form-control-sm" placeholder="Observações (opcional)"></div>
            <div class="col-md-1 form-check ms-2 align-self-center mb-0">
                <input class="form-check-input" type="checkbox" name="presente" value="1" id="presente" checked>
                <label class="form-check-label small" for="presente">Presente</label>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Registrar</button></div>
        </form>
        <?php endif; ?>

        <?php if (empty($encontros)): ?>
            <p class="text-muted text-center py-3 mb-0">Nenhum encontro registrado ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Data</th><th>Etapa</th><th>Presença</th><th>Observações</th></tr></thead>
                    <tbody>
                    <?php foreach ($encontros as $e): ?>
                        <tr>
                            <td><?= formatar_data($e['data'] ?? null) ?></td>
                            <td><?= esc($e['etapa'] ?: '-') ?></td>
                            <td><?= !empty($e['presente']) ? '<span class="badge bg-success">Presente</span>' : '<span class="badge bg-danger">Ausente</span>' ?></td>
                            <td><?= esc($e['observacoes'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
