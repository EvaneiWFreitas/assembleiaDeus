<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php if (session()->getFlashdata('erros')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                <li><?= $erro ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h4 class="fw-bold mb-3">
    <i class="fa-solid fa-hand-holding-dollar me-2 text-success"></i><?= esc($registro ? 'Editar Dízimo' : 'Novo Dízimo') ?>
</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?= form_open($registro ? 'dizimos/atualizar/' . $registro['id'] : 'dizimos/salvar') ?>
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="membro_id" class="form-label">Membro <span class="text-danger">*</span></label>
                    <select id="membro_id" name="membro_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($membros as $id => $nome): ?>
                            <option value="<?= $id ?>" <?= ($registro['membro_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= esc($nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="data" class="form-label">Data <span class="text-danger">*</span></label>
                    <input type="date" id="data" name="data" class="form-control" required
                        value="<?= old('data', esc($registro['data'] ?? date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-2">
                    <label for="valor" class="form-label">Valor (R$) <span class="text-danger">*</span></label>
                    <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01" required
                        value="<?= old('valor', $registro['valor'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label for="forma_pagamento" class="form-label">Forma Pgto</label>
                    <select id="forma_pagamento" name="forma_pagamento" class="form-select">
                        <option value="">Selecione...</option>
                        <?php foreach ($formas as $f): ?>
                            <option value="<?= $f ?>" <?= ($registro['forma_pagamento'] ?? '') === $f ? 'selected' : '' ?>>
                                <?= $f ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea id="observacoes" name="observacoes" class="form-control" rows="2"><?= old('observacoes', esc($registro['observacoes'] ?? '')) ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Salvar</button>
                <a href="<?= site_url('dizimos') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
