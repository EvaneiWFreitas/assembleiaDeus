<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-house-chimney-user me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <a href="<?= site_url('celulas') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= isset($registro) ? site_url('celulas/atualizar/' . $registro['id']) : site_url('celulas/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome'] ?? old('nome')) ?>" placeholder="Ex.: Célula Getsemani">
            </div>
            <div class="col-md-2">
                <label class="form-label">Código <span class="text-danger">*</span></label>
                <input type="text" name="codigo" class="form-control" required value="<?= esc($registro['codigo'] ?? old('codigo')) ?>" placeholder="Ex.: C001">
            </div>
            <div class="col-md-3">
                <label class="form-label">Congregação</label>
                <select name="congregacao_id" class="form-select">
                    <option value="">— Nenhuma —</option>
                    <?php foreach (($congregacoes ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['congregacao_id'] ?? old('congregacao_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Líder</label>
                <select name="lider_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['lider_id'] ?? old('lider_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Vice-Líder</label>
                <select name="vice_lider_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['vice_lider_id'] ?? old('vice_lider_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dia da Semana</label>
                <select name="dia_semana" class="form-select">
                    <option value="">— Selecione —</option>
                    <?php foreach (($dias ?? []) as $dia): ?>
                        <option value="<?= esc($dia) ?>" <?= ($registro['dia_semana'] ?? old('dia_semana')) === $dia ? 'selected' : '' ?>><?= esc($dia) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Horário</label>
                <input type="time" name="horario" class="form-control" value="<?= esc($registro['horario'] ?? old('horario')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="<?= esc($registro['endereco'] ?? old('endereco')) ?>">
            </div>
            <div class="col-md-6 form-check form-switch ms-2 align-self-center">
                <input class="form-check-input" type="checkbox" role="switch" name="ativo" value="1" id="ativo" <?= ($registro['ativo'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ativo">Ativa</label>
            </div>

            <?php if (isset($registro)): ?>
            <div class="col-12">
                <label class="form-label">Participantes</label>
                <?php $idsSelecionados = array_column($participantes ?? [], 'membro_id'); ?>
                <select name="participantes[]" class="form-select" multiple size="8">
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= in_array($id, $idsSelecionados) ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('celulas') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
