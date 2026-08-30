<?= $this->extend('auth/layout_login') ?>
<?= $this->section('conteudo') ?>
<div class="text-center mb-4">
    <i class="fa-solid fa-church fa-3x text-primary"></i>
    <h3 class="mt-2 fw-bold">Gestão de Igrejas</h3>
    <p class="text-muted">Acesse sua conta para continuar</p>
</div>

<?php if (session()->getFlashdata('erro')): ?>
    <div class="alert alert-danger py-2"><i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('erro')) ?></div>
<?php endif; ?>
<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= esc($e) ?></div>
<?php endforeach ?>

<form method="post" action="<?= site_url('login') ?>" autocomplete="off">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">E-mail <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required autofocus>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Senha <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="senha" class="form-control" required minlength="6">
            <button type="button" class="btn btn-outline-secondary" id="btnVerSenha" tabindex="-1"><i class="fa-solid fa-eye"></i></button>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar
    </button>
</form>

<p class="text-center text-muted small mt-4 mb-0">Sistema de Gestão de Igrejas &copy; <?= date('Y') ?></p>
<?= $this->endSection() ?>
