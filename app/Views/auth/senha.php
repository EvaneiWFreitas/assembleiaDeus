<?= $this->extend('auth/layout_login') ?>
<?= $this->section('conteudo') ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-key me-2 text-primary"></i>Alterar Senha</h4>

<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><?= esc($e) ?></div>
<?php endforeach ?>

<form method="post" action="<?= site_url('senha') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Senha Atual <span class="text-danger">*</span></label>
        <input type="password" name="senha_atual" class="form-control" required autocomplete="current-password">
    </div>
    <div class="mb-3">
        <label class="form-label">Nova Senha <span class="text-danger">*</span></label>
        <input type="password" name="senha" class="form-control" required minlength="8" autocomplete="new-password">
        <div class="form-text">Mínimo de 8 caracteres.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmar Nova Senha <span class="text-danger">*</span></label>
        <input type="password" name="confirma_senha" class="form-control" required autocomplete="new-password">
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fa-solid fa-check me-1"></i>Salvar</button>
        <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
<?= $this->endSection() ?>
