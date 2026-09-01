<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-images me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>
        <?php if (session('erro')): ?>
            <div class="alert alert-danger py-2"><?= esc(session('erro')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $ehEdicao ? site_url('banners/atualizar/' . $registro['id']) : site_url('banners/salvar') ?>"
              enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>
            <?php if ($ehEdicao): ?><input type="hidden" name="id" value="<?= (int) $registro['id'] ?>"><?php endif ?>

            <div class="col-md-8">
                <label class="form-label">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control" required minlength="3" maxlength="150"
                       placeholder="Ex.: Estamos em uma grande obra"
                       value="<?= esc($registro['titulo'] ?? old('titulo')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Ordem de exibição</label>
                <input type="number" name="ordem" class="form-control" min="0" value="<?= esc($registro['ordem'] ?? old('ordem', 0)) ?>">
                <div class="form-text">Menor valor aparece primeiro.</div>
            </div>
            <div class="col-12">
                <label class="form-label">Subtítulo</label>
                <input type="text" name="subtitulo" class="form-control" maxlength="255"
                       placeholder="Ex.: Uma igreja comprometida com a Palavra, com as almas e com o Reino de Deus."
                       value="<?= esc($registro['subtitulo'] ?? old('subtitulo')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Link (opcional)</label>
                <input type="url" name="link" class="form-control" maxlength="191"
                       placeholder="https://..." value="<?= esc($registro['link'] ?? old('link')) ?>">
                <div class="form-text">Se preenchido, o banner inteiro vira um link.</div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Imagem de fundo <?= $ehEdicao ? '(deixe vazio para manter a atual)' : '<span class="text-danger">*</span>' ?></label>
                <input type="file" name="imagem" class="form-control" accept="image/png,image/jpeg,image/webp" <?= $ehEdicao ? '' : 'required' ?>>
                <div class="form-text">PNG, JPG ou WebP · máximo 5MB · recomendado 16:9 (1600px+).</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Situação</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>

            <?php if ($ehEdicao && ! empty($registro['imagem']) && is_file(ROOTPATH . 'public/uploads/banners/' . $registro['imagem'])): ?>
            <div class="col-12">
                <label class="form-label">Prévia atual</label>
                <div>
                    <img src="<?= base_url('uploads/banners/' . $registro['imagem']) ?>" alt="Prévia"
                         style="max-width:100%;max-height:220px;border-radius:10px;" class="border bg-light">
                </div>
            </div>
            <?php endif; ?>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('banners') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>