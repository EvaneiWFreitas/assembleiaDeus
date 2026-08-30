<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('roles/atualizar/' . $registro['id']) : site_url('roles/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-5">
                <label class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required minlength="3" value="<?= esc($registro['nome'] ?? old('nome')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Identificador (slug)</label>
                <input type="text" name="slug" class="form-control" placeholder="gerado automaticamente" value="<?= esc($registro['slug'] ?? old('slug')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Descrição</label>
                <input type="text" name="descricao" class="form-control" value="<?= esc($registro['descricao'] ?? old('descricao')) ?>">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('roles') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php if ($ehEdicao && ! empty($permissoes)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="fa-solid fa-list-check me-2 text-primary"></i>Permissões do Perfil</div>
    <div class="card-body">
        <form method="post" action="<?= site_url('roles/atualizar/' . $registro['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="nome" value="<?= esc($registro['nome']) ?>">
            <input type="hidden" name="slug" value="<?= esc($registro['slug']) ?>">
            <input type="hidden" name="descricao" value="<?= esc($registro['descricao']) ?>">

            <div class="row">
                <?php foreach ($permissoes as $modulo => $lista): ?>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold text-uppercase small text-primary mb-2"><?= esc($modulo) ?></div>
                            <?php foreach ($lista as $p): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissoes[]"
                                           value="<?= $p['id'] ?>" id="p<?= $p['id'] ?>"
                                           <?= in_array($p['id'], $selecionadas) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="p<?= $p['id'] ?>"><?= esc($p['acao']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-1"></i>Salvar Permissões</button>
        </form>
    </div>
</div>
<?php elseif ($ehEdicao): ?>
<div class="alert alert-info">Nenhuma permissão cadastrada no sistema. Rode o seeder <code>php spark db:seed PermissoesSeeder</code>.</div>
<?php endif; ?>

<?= $this->endSection() ?>
