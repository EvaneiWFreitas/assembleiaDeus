<div class="admin-auth-cabecalho">
    <div class="admin-auth-icone"><i class="fas fa-right-to-bracket"></i></div>
    <h1 class="h4 mb-1">Entrar no painel</h1>
    <p class="text-muted small mb-0">Use sua conta administrativa para continuar.</p>
</div>

<form method="post" action="<?= e(url('admin/login')) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="mb-3">
        <label for="login_email" class="form-label">E-mail</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="login_email" name="email"
                   value="<?= e(old('email')) ?>" required autofocus autocomplete="username"
                   placeholder="voce@igreja.com">
        </div>
    </div>

    <div class="mb-4">
        <label for="login_senha" class="form-label">Senha</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="login_senha" name="senha"
                   required autocomplete="current-password" placeholder="Sua senha">
        </div>
    </div>

    <button type="submit" class="btn btn-ad-primary w-100">
        <i class="fas fa-right-to-bracket me-2"></i>Entrar
    </button>
</form>

<p class="text-center text-muted small mt-4 mb-0">
    Esqueceu sua senha? Solicite a redefinição ao administrador do sistema.
</p>
