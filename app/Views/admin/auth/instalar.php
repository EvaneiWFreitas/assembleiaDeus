<div class="admin-auth-cabecalho">
    <div class="admin-auth-icone"><i class="fas fa-wrench"></i></div>
    <h1 class="h4 mb-1">Instalação inicial</h1>
    <p class="text-muted small mb-0">Crie o primeiro administrador com perfil Superadministrador.</p>
</div>

<div class="alert alert-info small">
    <i class="fas fa-circle-info me-2"></i>
    Esta tela só fica disponível enquanto não existir nenhum usuário cadastrado.
</div>

<form method="post" action="<?= e(url('admin/instalar')) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="mb-3">
        <label for="inst_nome" class="form-label">Nome completo</label>
        <input type="text" class="form-control" id="inst_nome" name="nome"
               value="<?= e(old('nome')) ?>" required minlength="3" maxlength="150"
               autocomplete="name" placeholder="Nome do administrador">
    </div>

    <div class="mb-3">
        <label for="inst_email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="inst_email" name="email"
               value="<?= e(old('email')) ?>" required maxlength="190"
               autocomplete="username" placeholder="voce@igreja.com">
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            <label for="inst_senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="inst_senha" name="senha"
                   required minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
        </div>
        <div class="col-sm-6">
            <label for="inst_senha2" class="form-label">Confirmar senha</label>
            <input type="password" class="form-control" id="inst_senha2" name="senha_confirmacao"
                   required minlength="8" autocomplete="new-password" placeholder="Repita a senha">
        </div>
    </div>

    <button type="submit" class="btn btn-ad-primary w-100 mt-4">
        <i class="fas fa-check me-2"></i>Criar administrador
    </button>
</form>
