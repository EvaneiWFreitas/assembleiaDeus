<?php
$valor = static fn (string $campo, mixed $padrao = ''): string => (string) old($campo, $foto[$campo] ?? $padrao);
$idFoto = (int) ($foto['id'] ?? 0);
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/galeria')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<form method="post" action="<?= e($acao) ?>" enctype="multipart/form-data" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Fotografia</h2>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="campo_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_titulo" name="titulo"
                           value="<?= e($valor('titulo')) ?>" placeholder="Ex.: Culto de ação de graças" maxlength="150">
                    <div class="form-text">Usado como legenda na galeria e como texto alternativo da imagem.</div>
                </div>
                <div class="col-lg-6">
                    <label for="campo_descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="campo_descricao" name="descricao"
                           value="<?= e($valor('descricao')) ?>" placeholder="Descrição curta (opcional)" maxlength="255">
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-image me-2"></i>Imagem</h2>
            <div class="row g-3 align-items-start">
                <div class="col-lg-7">
                    <label for="campo_imagem" class="form-label">Arquivo de imagem</label>
                    <input type="file" class="form-control" id="campo_imagem" name="imagem"
                           accept="image/jpeg,image/png,image/webp" <?= $idFoto === 0 ? 'required' : '' ?>>
                    <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a imagem atual.</div>
                </div>
                <?php if (!empty($foto['imagem'])): ?>
                    <div class="col-lg-5">
                        <div class="small text-muted mb-2">Imagem atual</div>
                        <img src="<?= e(url('uploads/' . $foto['imagem'])) ?>" alt="Imagem atual da fotografia" class="admin-upload-preview">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Publicação</h2>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="campo_evento" class="form-label">Evento relacionado</label>
                    <select class="form-select" id="campo_evento" name="evento_id">
                        <option value="0">Nenhum</option>
                        <?php foreach ($eventos as $evento): ?>
                            <option value="<?= (int) $evento['id'] ?>" <?= (int) $valor('evento_id', '0') === (int) $evento['id'] ? 'selected' : '' ?>>
                                <?= e($evento['titulo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Opcional. Vincula a fotografia a um evento cadastrado.</div>
                </div>
                <div class="col-lg-3">
                    <label for="campo_ordem" class="form-label">Ordem de exibição</label>
                    <input type="number" class="form-control" id="campo_ordem" name="ordem"
                           value="<?= e($valor('ordem', '0')) ?>" min="0" max="65535">
                    <div class="form-text">Menores valores aparecem primeiro.</div>
                </div>
                <div class="col-lg-3 d-flex align-items-end pb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="campo_ativa"
                               name="ativa" value="1" <?= $valor('ativa', '1') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_ativa">Exibir no site</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= e(url('admin/galeria')) ?>" class="btn btn-ad-outline">Cancelar</a>
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar fotografia
        </button>
    </div>
</form>
