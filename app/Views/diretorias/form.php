<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-user-tie me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('diretorias/atualizar/' . $registro['id']) : site_url('diretorias/salvar') ?>" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>

            <div class="col-md-3 d-flex flex-column align-items-center">
                <?php
                    $fotoAtual   = $registro['foto'] ?? null;
                    $temFoto     = $ehEdicao && ! empty($fotoAtual) && is_file(ROOTPATH . 'public/uploads/diretorias/' . $fotoAtual);
                ?>
                <?php if ($temFoto): ?>
                    <img id="foto-preview" src="<?= base_url('uploads/diretorias/' . $fotoAtual) ?>" alt="Foto atual"
                         class="rounded-circle mb-2" style="width:130px;height:130px;object-fit:cover;border:3px solid #e9ecef;">
                <?php else: ?>
                    <div id="foto-preview" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-2"
                         style="width:130px;height:130px;font-size:2.8rem;border:3px solid #e9ecef;"><i class="fa-solid fa-user-tie"></i></div>
                <?php endif; ?>
                <label class="form-label text-center">Foto do membro</label>
                <input type="file" name="foto" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp" onchange="mostrarPreview(this)">
                <div class="form-text text-center">PNG, JPG ou WebP · máx. 5MB</div>
            </div>

            <div class="col-md-9">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome'] ?? old('nome')) ?>" placeholder="Ex.: Pr. João da Silva">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cargo <span class="text-danger">*</span></label>
                        <input type="text" name="cargo" class="form-control" required value="<?= esc($registro['cargo'] ?? old('cargo')) ?>" placeholder="Ex.: Pastor Presidente">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($registro['email'] ?? old('email')) ?>" placeholder="email@exemplo.com">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="<?= esc($registro['telefone'] ?? old('telefone')) ?>" placeholder="(00) 0000-0000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= esc($registro['whatsapp'] ?? old('whatsapp')) ?>" placeholder="(00) 00000-0000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" min="0" class="form-control" value="<?= esc($registro['ordem'] ?? old('ordem') ?? 0) ?>">
                        <div class="form-text">Define a ordem de exibição no site.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Situação</label>
                        <select name="ativo" class="form-select">
                            <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                            <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('diretorias') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script {csp-script-nonce}>
function mostrarPreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var el = document.getElementById('foto-preview');
            el.outerHTML = '<img id="foto-preview" src="' + e.target.result + '" class="rounded-circle mb-2" style="width:130px;height:130px;object-fit:cover;border:3px solid #e9ecef;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?= $this->endSection() ?>
