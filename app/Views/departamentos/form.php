<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-<?= isset($registro) ? 'pen' : 'plus' ?> me-2 text-primary"></i><?= isset($registro) ? 'Editar Departamento' : 'Novo Departamento' ?></h4>
    <a href="<?= site_url('departamentos') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= isset($registro) ? site_url('departamentos/atualizar/' . $registro['id']) : site_url('departamentos/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome'] ?? '') ?>"
            </div>
            <div class="col-md-6">
                <label class="form-label">Líder</label>
                <select name="lider_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['lider_id'] ?? null) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vice-Líder</label>
                <select name="vice_lider_id" class="form-select">
                    <option value="">— Nenhum —</option>
                    <?php foreach (($membros ?? []) as $id => $nome): ?>
                        <option value="<?= $id ?>" <?= ($registro['vice_lider_id'] ?? null) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cor</label>
                <input type="color" name="cor" class="form-control form-control-color" value="<?= esc($registro['cor'] ?? '#0d6efd') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"><?= esc($registro['descricao'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6 form-check form-switch ms-2 align-self-center">
                <input class="form-check-input" type="checkbox" role="switch" name="ativo" value="1" id="ativo" <?= ($registro['ativo'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('departamentos') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
