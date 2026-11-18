<?php
$valorCampo = static function (string $nome, mixed $padrao = '') use ($usuario): mixed {
    return old($nome, $usuario[$nome] ?? $padrao);
};
$campoMarcado = static function (string $nome, bool $padrao) use ($usuario): bool {
    $valor = $_SESSION['old'][$nome] ?? ($usuario[$nome] ?? ($padrao ? 1 : 0));

    return (int) $valor === 1;
};
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($titulo) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url($voltar)) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<form method="post" action="<?= e($acao) ?>" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="campo_nome" class="form-label">Nome completo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="campo_nome" name="nome"
                           value="<?= e($valorCampo('nome')) ?>" maxlength="150" required
                           autocomplete="name" placeholder="Nome do usuário">
                </div>
                <div class="col-md-4">
                    <label for="campo_telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="campo_telefone" name="telefone"
                           value="<?= e($valorCampo('telefone')) ?>" maxlength="20"
                           autocomplete="tel" placeholder="(00) 00000-0000">
                </div>
                <div class="col-md-6">
                    <label for="campo_email" class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="campo_email" name="email"
                           value="<?= e($valorCampo('email')) ?>" maxlength="190" required
                           autocomplete="email" placeholder="usuario@igreja.com">
                </div>
                <div class="col-md-6">
                    <label for="campo_perfil_id" class="form-label">Perfil de acesso <span class="text-danger">*</span></label>
                    <select class="form-select" id="campo_perfil_id" name="perfil_id" required>
                        <option value="">Selecione um perfil...</option>
                        <?php foreach ($perfis as $id => $perfil): ?>
                            <option value="<?= e($id) ?>"
                                    <?= (string) $valorCampo('perfil_id') === (string) $id ? 'selected' : '' ?>>
                                <?= e($perfil['nome']) ?> (nível <?= (int) $perfil['nivel'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="campo_congregacao_id" class="form-label">Congregação</label>
                    <select class="form-select" id="campo_congregacao_id" name="congregacao_id">
                        <option value="">Sem congregação vinculada</option>
                        <?php foreach ($congregacoes as $id => $nome): ?>
                            <option value="<?= e($id) ?>"
                                    <?= (string) $valorCampo('congregacao_id') === (string) $id ? 'selected' : '' ?>>
                                <?= e($nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="campo_senha" class="form-label">
                        Senha <?= $edicao ? '' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" class="form-control" id="campo_senha" name="senha"
                           minlength="8" autocomplete="new-password"
                           <?= $edicao ? '' : 'required' ?>>
                    <div class="form-text">
                        <?= $edicao ? 'Deixe em branco para manter a senha atual. Mínimo de 8 caracteres.' : 'Mínimo de 8 caracteres.' ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="campo_senha_confirmacao" class="form-label">
                        Confirmar senha <?= $edicao ? '' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" class="form-control" id="campo_senha_confirmacao"
                           name="senha_confirmacao" minlength="8" autocomplete="new-password"
                           <?= $edicao ? '' : 'required' ?>>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="campo_ativo" name="ativo" value="1"
                               <?= $campoMarcado('ativo', true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_ativo">Conta ativa</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="campo_must_change_password" name="must_change_password" value="1"
                               <?= $campoMarcado('must_change_password', true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_must_change_password">
                            Exigir troca de senha no próximo acesso
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex flex-wrap justify-content-end gap-2 pb-4">
            <a href="<?= e(url($voltar)) ?>" class="btn btn-ad-outline">Cancelar</a>
            <button type="submit" class="btn btn-ad-primary">
                <i class="fas fa-floppy-disk me-1"></i><?= e($botao) ?>
            </button>
        </div>
    </div>
</form>
