<div class="admin-auth-cabecalho">
    <div class="admin-auth-icone"><i class="fas fa-key"></i></div>
    <h1 class="h4 mb-1">Alterar senha</h1>
    <p class="text-muted small mb-0">Escolha uma nova senha para proteger sua conta.</p>
</div>

<?php if ((int) (\App\Core\Auth::user()['must_change_password'] ?? 0) === 1): ?>
    <div class="alert alert-warning small">
        <i class="fas fa-triangle-exclamation me-2"></i>
        Por segurança, altere a senha antes de acessar o painel.
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('admin/trocar-senha')) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="mb-3">
        <label for="senha_atual" class="form-label">Senha atual</label>
        <input type="password" class="form-control" id="senha_atual" name="senha_atual"
               required autocomplete="current-password">
    </div>

    <div class="mb-3">
        <label for="senha_nova" class="form-label">Nova senha</label>
        <input type="password" class="form-control" id="senha_nova" name="senha_nova"
               required minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
    </div>

    <div class="mb-4">
        <label for="senha_confirmacao" class="form-label">Confirmar nova senha</label>
        <input type="password" class="form-control" id="senha_confirmacao" name="senha_confirmacao"
               required minlength="8" autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-ad-primary w-100">
        <i class="fas fa-check me-2"></i>Salvar nova senha
    </button>
</form>
