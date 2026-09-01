<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-place-of-worship me-2 text-primary"></i><?= esc($titulo) ?></h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($erros)) foreach ($erros as $e): ?>
            <div class="alert alert-warning py-2"><?= esc($e) ?></div>
        <?php endforeach ?>

        <form method="post" action="<?= $ehEdicao ? site_url('congregacoes/atualizar/' . $registro['id']) : site_url('congregacoes/salvar') ?>" class="row g-3">
            <?= csrf_field() ?>
            <?php if ($ehEdicao): ?><input type="hidden" name="id" value="<?= (int) $registro['id'] ?>"><?php endif ?>
            <div class="col-md-6">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required minlength="3" value="<?= esc($registro['nome'] ?? old('nome')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Código <span class="text-danger">*</span></label>
                <input type="text" name="codigo" class="form-control" required value="<?= esc($registro['codigo'] ?? old('codigo')) ?>" placeholder="Ex.: CONG-01">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Abertura</label>
                <input type="date" name="data_abertura" class="form-control" value="<?= esc($registro['data_abertura'] ?? old('data_abertura')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control mascara-telefone" value="<?= esc($registro['telefone'] ?? old('telefone')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= esc($registro['email'] ?? old('email')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" class="form-control mascara-cep" value="<?= esc($registro['cep'] ?? old('cep')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Situação</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?= ($registro['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativa</option>
                    <option value="0" <?= ($registro['ativo'] ?? 1) == 0 ? 'selected' : '' ?>>Inativa</option>
                </select>
            </div>
            <div class="col-md-7">
                <label class="form-label">Logradouro</label>
                <input type="text" name="logradouro" class="form-control" value="<?= esc($registro['logradouro'] ?? old('logradouro')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" value="<?= esc($registro['numero'] ?? old('numero')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" class="form-control" value="<?= esc($registro['bairro'] ?? old('bairro')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" value="<?= esc($registro['cidade'] ?? old('cidade')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">UF</label>
                <input type="text" name="estado" class="form-control" maxlength="2" value="<?= esc($registro['estado'] ?? old('estado')) ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
                <a href="<?= site_url('congregacoes') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
