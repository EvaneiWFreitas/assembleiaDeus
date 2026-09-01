<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-user-tie me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('obreiros/atualizar/' . $registro['id']) : site_url('obreiros/salvar') ?>" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-3 d-flex flex-column align-items-center">
                <?php
                    $fotoObreiro = $registro['foto'] ?? null;
                    $temFoto     = $ehEdicao && ! empty($fotoObreiro) && is_file(ROOTPATH . 'public/uploads/obreiros/' . $fotoObreiro);
                ?>
                <?php if ($temFoto): ?>
                    <img id="foto-preview" src="<?= base_url('uploads/obreiros/' . $fotoObreiro) ?>" alt="Foto atual"
                         class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;border:3px solid #e9ecef;">
                <?php else: ?>
                    <div id="foto-preview" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-2"
                         style="width:120px;height:120px;font-size:2.5rem;border:3px solid #e9ecef;"><i class="fa-solid fa-user-tie"></i></div>
                <?php endif; ?>
                <label class="form-label text-center">Foto do obreiro</label>
                <input type="file" name="foto" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp" onchange="mostrarPreview(this)">
                <div class="form-text text-center">PNG, JPG ou WebP · máx 5MB</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Membro <span class="text-danger">*</span></label>
                <select name="membro_id" class="form-select" required>
                    <option value="">Selecione o membro...</option>
                    <?php foreach ($membros as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['membro_id'] ?? old('membro_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">O obreiro é sempre vinculado a um cadastro de membro. Se ainda não for membro, cadastre-o em <a href="<?= site_url('membros/novo') ?>">Membros</a>.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                <select name="cargo" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= esc($c) ?>" <?= ($registro['cargo'] ?? old('cargo')) === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Consagração</label>
                <input type="date" name="data_conexao" class="form-control" value="<?= esc($registro['data_conexao'] ?? old('data_conexao')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Congregação</label>
                <select name="congregacao_id" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($congregacoes as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['congregacao_id'] ?? old('congregacao_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de Registro</label>
                <input type="text" name="numero_registro" class="form-control" value="<?= esc($registro['numero_registro'] ?? old('numero_registro')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Situação</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="2"><?= esc($registro['observacoes'] ?? old('observacoes')) ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('obreiros') ?>" class="btn btn-secondary">Cancelar</a>
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
            el.outerHTML = '<img id="foto-preview" src="' + e.target.result + '" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;border:3px solid #e9ecef;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?= $this->endSection() ?>
