<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-briefcase me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <a href="<?= site_url('cargos') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<?php if (session('erro')): ?>
    <div class="alert alert-danger py-2"><?= esc(session('erro')) ?></div>
<?php endif; ?>

<?= validation_list_errors() ?>

<form method="post" action="<?= esc($acao) ?>" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-md-6">
        <label for="nome" class="form-label">Nome do Cargo <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="nome" name="nome" value="<?= esc(old('nome', $cargo['nome'] ?? '')) ?>" required maxlength="100">
    </div>

    <div class="col-md-3">
        <label for="ordem" class="form-label">Ordem</label>
        <input type="number" class="form-control" id="ordem" name="ordem" value="<?= esc(old('ordem', $cargo['ordem'] ?? 0)) ?>" min="0">
    </div>

    <div class="col-md-3">
        <label for="ativo" class="form-label">Situação</label>
        <select class="form-select" id="ativo" name="ativo">
            <option value="1" <?= (old('ativo', $cargo['ativo'] ?? 1) == 1 ? 'selected' : '') ?>>Ativo</option>
            <option value="0" <?= (old('ativo', $cargo['ativo'] ?? 1) == 0 ? 'selected' : '') ?>>Inativo</option>
        </select>
    </div>

    <div class="col-12">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= esc(old('descricao', $cargo['descricao'] ?? '')) ?></textarea>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Salvar</button>
        <a href="<?= site_url('cargos') ?>" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>