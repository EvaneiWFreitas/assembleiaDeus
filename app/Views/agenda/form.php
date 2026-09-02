<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>
<?php $val = static fn (string $campo) => esc($registro[$campo] ?? old($campo)); ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-calendar-days me-2 text-primary"></i><?= esc($titulo) ?></h4>

<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><?= esc($e) ?></div>
<?php endforeach ?>

<form method="post" action="<?= $ehEdicao ? site_url('agenda/atualizar/' . $registro['id']) : site_url('agenda/salvar') ?>" enctype="multipart/form-data" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-12"><h6 class="text-primary fw-semibold">Dados do Evento</h6></div>
    <div class="col-md-8">
        <label class="form-label">Título <span class="text-danger">*</span></label>
        <input type="text" name="titulo" class="form-control" required minlength="3" maxlength="150" value="<?= $val('titulo') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipo <span class="text-danger">*</span></label>
        <select name="tipo" class="form-select" required>
            <?php foreach (['Reunião', 'Culto', 'Evento', 'Retiro', 'Encontro', 'Outro'] as $t): ?>
                <option value="<?= $t ?>" <?= ($registro['tipo'] ?? old('tipo')) === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Data de Início <span class="text-danger">*</span></label>
        <input type="date" name="data_inicio" class="form-control" required value="<?= $val('data_inicio') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data de Término</label>
        <input type="date" name="data_fim" class="form-control" value="<?= $val('data_fim') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Horário de Início</label>
        <input type="time" name="hora_inicio" class="form-control" value="<?= $val('hora_inicio') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Horário de Término</label>
        <input type="time" name="hora_fim" class="form-control" value="<?= $val('hora_fim') ?>">
    </div>

    <div class="col-md-5">
        <label class="form-label">Local</label>
        <input type="text" name="local" class="form-control" maxlength="191" placeholder="Ex: Templo Central, Salão de Eventos..." value="<?= $val('local') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Responsável</label>
        <input type="text" name="responsavel" class="form-control" maxlength="150" value="<?= $val('responsavel') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Foto do Responsável</label>
        <div class="d-flex align-items-center gap-2">
            <?php
                $fotoResp = $registro['foto_responsavel'] ?? null;
                $temFotoResp = $ehEdicao && !empty($fotoResp) && is_file(ROOTPATH . 'public/uploads/agenda/' . $fotoResp);
            ?>
            <?php if ($temFotoResp): ?>
                <img id="foto-responsavel-preview" src="<?= base_url('uploads/agenda/' . $fotoResp) ?>" alt="Foto do responsável"
                     class="rounded-circle" style="width:48px;height:48px;object-fit:cover;min-width:48px;">
            <?php else: ?>
                <i id="foto-responsavel-preview" class="fa-solid fa-user text-muted fa-lg"></i>
            <?php endif; ?>
            <input type="file" name="foto_responsavel" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp" onchange="mostrarPreviewResp(this)">
        </div>
        <div class="form-text">PNG, JPG ou WebP · máx 5MB</div>
    </div>
    <div class="col-md-3">
        <label class="form-label">Congregação</label>
        <select name="congregacao_id" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach ($congregacoes as $id => $nome): ?>
                <option value="<?= $id ?>" <?= ($registro['congregacao_id'] ?? old('congregacao_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Recorrência</label>
        <select name="recorrencia" class="form-select">
            <option value="">Nenhuma</option>
            <?php foreach (['semanal' => 'Semanal', 'quinzenal' => 'Quinzenal', 'mensal' => 'Mensal'] as $v => $l): ?>
                <option value="<?= $v ?>" <?= ($registro['recorrencia'] ?? old('recorrencia')) === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Cor</label>
        <input type="color" name="cor" class="form-control form-control-color" value="<?= $val('cor') ?: '#0d6efd' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <?php foreach (['Pendente', 'Confirmado', 'Cancelado', 'Concluído'] as $s): ?>
                <option value="<?= $s ?>" <?= ($registro['status'] ?? 'Pendente') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3"><?= $val('descricao') ?></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Observações</label>
        <textarea name="observacoes" class="form-control" rows="2"><?= $val('observacoes') ?></textarea>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
        <a href="<?= site_url('agenda') ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?= $this->section('scripts') ?>
<script {csp-script-nonce}>
function mostrarPreviewResp(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var el = document.getElementById('foto-responsavel-preview');
            if (el.tagName === 'IMG') {
                el.src = e.target.result;
            } else {
                el.outerHTML = '<img id="foto-responsavel-preview" src="' + e.target.result + '" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;min-width:48px;">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>