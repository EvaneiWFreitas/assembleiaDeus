<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3">
    <i class="fa-solid fa-user-gear me-2 text-primary"></i><?= esc($titulo) ?>
</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('usuarios/atualizar/' . $registro['id']) : site_url('usuarios/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required minlength="3"
                       value="<?= esc($registro['nome'] ?? old('nome')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required
                       value="<?= esc($registro['email'] ?? old('email')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control mascara-telefone"
                       value="<?= esc($registro['telefone'] ?? old('telefone')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Perfil <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($registro['role_id'] ?? old('role_id')) == $r['id'] ? 'selected' : '' ?>>
                            <?= esc($r['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Situação</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Senha <?= $ehEdicao ? '<span class="text-muted small">(deixe em branco para manter)</span>' : '<span class="text-danger">*</span>' ?></label>
                <div class="input-group">
                    <input type="password" name="senha" id="senha" class="form-control" <?= $ehEdicao ? '' : 'required' ?> minlength="8" autocomplete="new-password">
                    <button type="button" class="btn btn-outline-secondary" onclick="alternarSenha('senha', this)" tabindex="-1" title="Mostrar/ocultar"><i class="fa-solid fa-eye"></i></button>
                </div>
                <div class="form-text">Mínimo de 8 caracteres.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Confirmar Senha <?= $ehEdicao ? '' : '<span class="text-danger">*</span>' ?></label>
                <div class="input-group">
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" <?= $ehEdicao ? '' : 'required' ?> minlength="8" autocomplete="new-password">
                    <button type="button" class="btn btn-outline-secondary" onclick="alternarSenha('confirmar_senha', this)" tabindex="-1" title="Mostrar/ocultar"><i class="fa-solid fa-eye"></i></button>
                </div>
                <div class="form-text">Repita a senha para confirmação.</div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script {csp-script-nonce}>
function alternarSenha(campoId, botao) {
    const campo = document.getElementById(campoId);
    const icone = botao.querySelector('i');
    if (campo.type === 'password') {
        campo.type = 'text';
        icone.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        campo.type = 'password';
        icone.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', (e) => {
            const s = document.getElementById('senha').value;
            const c = document.getElementById('confirmar_senha').value;
            if (s !== '' && s !== c) {
                e.preventDefault();
                alert('As senhas não coincidem. Verifique e tente novamente.');
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
