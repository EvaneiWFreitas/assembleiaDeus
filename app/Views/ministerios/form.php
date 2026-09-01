<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-hands-praying me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <a href="<?= site_url('ministerios') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= isset($registro) ? site_url('ministerios/atualizar/' . $registro['id']) : site_url('ministerios/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome'] ?? old('nome')) ?>" placeholder="Ex.: Ministério de Louvor">
            </div>
            <div class="col-md-6">
                <label class="form-label">Departamento</label>
                <select name="departamento_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($departamentos ?? []) as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($registro['departamento_id'] ?? old('departamento_id')) == $d['id'] ? 'selected' : '' ?>><?= esc($d['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Líder</label>
                <select name="lider_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['lider_id'] ?? old('lider_id')) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 form-check form-switch ms-2 align-self-center">
                <input class="form-check-input" type="checkbox" role="switch" name="ativo" value="1" id="ativo" <?= ($registro['ativo'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
            <div class="col-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"><?= esc($registro['descricao'] ?? old('descricao')) ?></textarea>
            </div>

            <?php if (isset($registro)): ?>
            <div class="col-12">
                <label class="form-label">Participantes <small class="text-muted">(o líder é incluído automaticamente)</small></label>
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
                <a href="<?= site_url('ministerios') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
