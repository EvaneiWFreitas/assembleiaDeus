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
    <i class="fa-solid fa-tag me-2 text-warning"></i><?= esc($registro ? 'Editar Categoria' : 'Nova Categoria') ?>
</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?= form_open($registro ? 'categorias/atualizar/' . $registro['id'] : 'categorias/salvar') ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" required
                        value="<?= old('nome', esc($registro['nome'] ?? '')) ?>" maxlength="80">
                </div>
                <div class="col-md-3">
                    <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select id="tipo" name="tipo" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t ?>" <?= ($registro['tipo'] ?? '') === $t ? 'selected' : '' ?>>
                                <?= ucfirst($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ativo" class="form-label">Status</label>
                    <select id="ativo" name="ativo" class="form-select">
                        <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Salvar</button>
                <a href="<?= site_url('categorias') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>
