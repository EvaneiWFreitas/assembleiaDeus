<?php
$valorCampo = static function (string $nome, mixed $padrao = '') use ($registro): mixed {
    return old($nome, $registro[$nome] ?? $padrao);
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
                <?php foreach ($campos as $campo): ?>
                    <?php
                    $nome        = (string) $campo['name'];
                    $valor       = $valorCampo($nome, $campo['default'] ?? '');
                    $tipo        = (string) ($campo['type'] ?? 'text');
                    $obrigatorio = !empty($campo['required']);
                    $classe      = (string) ($campo['col'] ?? 'col-md-6');
                    ?>
                    <div class="<?= e($classe) ?>">
                        <label for="campo_<?= e($nome) ?>" class="form-label">
                            <?= e($campo['label']) ?>
                            <?php if ($obrigatorio): ?><span class="text-danger">*</span><?php endif; ?>
                        </label>

                        <?php if ($tipo === 'textarea'): ?>
                            <textarea class="form-control" id="campo_<?= e($nome) ?>" name="<?= e($nome) ?>"
                                      rows="<?= (int) ($campo['rows'] ?? 3) ?>"
                                      <?= $obrigatorio ? 'required' : '' ?>
                                      <?php if (!empty($campo['maxlength'])): ?>maxlength="<?= (int) $campo['maxlength'] ?>"<?php endif; ?>
                                      placeholder="<?= e($campo['placeholder'] ?? '') ?>"><?= e($valor) ?></textarea>
                        <?php elseif ($tipo === 'select'): ?>
                            <select class="form-select" id="campo_<?= e($nome) ?>" name="<?= e($nome) ?>"
                                    <?= $obrigatorio ? 'required' : '' ?>>
                                <option value=""><?= e($campo['placeholder'] ?? 'Selecione...') ?></option>
                                <?php foreach (($campo['options'] ?? []) as $opcaoValor => $opcaoLabel): ?>
                                    <option value="<?= e($opcaoValor) ?>"
                                            <?= (string) $valor === (string) $opcaoValor ? 'selected' : '' ?>>
                                        <?= e($opcaoLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?= e($tipo) ?>" class="form-control" id="campo_<?= e($nome) ?>"
                                   name="<?= e($nome) ?>" value="<?= e($valor) ?>"
                                   <?= $obrigatorio ? 'required' : '' ?>
                                   <?php if (!empty($campo['minlength'])): ?>minlength="<?= (int) $campo['minlength'] ?>"<?php endif; ?>
                                   <?php if (!empty($campo['maxlength'])): ?>maxlength="<?= (int) $campo['maxlength'] ?>"<?php endif; ?>
                                   <?php if (($campo['step'] ?? '') !== ''): ?>step="<?= e($campo['step']) ?>"<?php endif; ?>
                                   placeholder="<?= e($campo['placeholder'] ?? '') ?>">
                        <?php endif; ?>

                        <?php if (!empty($campo['help'])): ?>
                            <div class="form-text"><?= e($campo['help']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex flex-wrap justify-content-end gap-2 pb-4">
            <a href="<?= e(url($voltar)) ?>" class="btn btn-ad-outline">Cancelar</a>
            <button type="submit" class="btn btn-ad-primary">
                <i class="fas fa-check me-1"></i><?= e($botao) ?>
            </button>
        </div>
    </div>
</form>
