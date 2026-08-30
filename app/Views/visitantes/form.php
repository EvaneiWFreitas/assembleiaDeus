<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>
<?php $val = static fn (string $campo) => esc($registro[$campo] ?? old($campo)); ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-user-plus me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('visitantes/atualizar/' . $registro['id']) : site_url('visitantes/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-8">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required minlength="3" value="<?= $val('nome') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data da Primeira Visita</label>
                <input type="date" name="data_primeira_visita" class="form-control" value="<?= $val('data_primeira_visita') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control mascara-telefone" value="<?= $val('telefone') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control mascara-telefone" value="<?= $val('whatsapp') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= $val('email') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="<?= $val('endereco') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Como conheceu a igreja?</label>
                <input type="text" name="como_conheceu" class="form-control" placeholder="Convite, redes sociais, familiar..." value="<?= $val('como_conheceu') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Culto Visitado</label>
                <input type="text" name="culto_visitado" class="form-control" value="<?= $val('culto_visitado') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Congregação</label>
                <select name="congregacao_id" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($congregacoes as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['congregacao_id'] ?? old('congregacao_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Responsável pelo Acompanhamento</label>
                <select name="membro_id" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($membros as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['membro_id'] ?? old('membro_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Etapa do Acompanhamento <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <?php foreach ($statusFunil as $s): ?>
                        <option value="<?= esc($s) ?>" <?= ($registro['status'] ?? 'Primeira visita') === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Ao definir "Membro", o cadastro de membro é criado automaticamente.</div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="2"><?= $val('observacoes') ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('visitantes') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
