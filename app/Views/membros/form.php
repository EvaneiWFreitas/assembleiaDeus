<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $registro = $registro ?? null; $ehEdicao = $registro !== null; ?>
<?php $val = static fn (string $campo) => esc($registro[$campo] ?? old($campo)); ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-user me-2 text-primary"></i><?= esc($titulo) ?></h4>

<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><?= esc($e) ?></div>
<?php endforeach ?>

<form method="post" action="<?= $ehEdicao ? site_url('membros/atualizar/' . $registro['id']) : site_url('membros/salvar') ?>" enctype="multipart/form-data" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-12"><h6 class="text-primary fw-semibold">Dados Pessoais</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
        <input type="text" name="nome" class="form-control" required minlength="3" value="<?= $val('nome') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Nome Social</label>
        <input type="text" name="nome_social" class="form-control" value="<?= $val('nome_social') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">CPF</label>
        <input type="text" name="cpf" class="form-control mascara-cpf" value="<?= $val('cpf') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">RG</label>
        <input type="text" name="rg" class="form-control" value="<?= $val('rg') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data de Nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" value="<?= $val('data_nascimento') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Sexo</label>
        <select name="sexo" class="form-select">
            <option value="">Selecione...</option>
            <option value="M" <?= ($registro['sexo'] ?? old('sexo')) === 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= ($registro['sexo'] ?? old('sexo')) === 'F' ? 'selected' : '' ?>>Feminino</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Estado Civil</label>
        <select name="estado_civil" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach (['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'União estável'] as $ec): ?>
                <option value="<?= $ec ?>" <?= ($registro['estado_civil'] ?? old('estado_civil')) === $ec ? 'selected' : '' ?>><?= $ec ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Nacionalidade</label>
        <input type="text" name="nacionalidade" class="form-control" value="<?= $val('nacionalidade') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Naturalidade</label>
        <input type="text" name="naturalidade" class="form-control" value="<?= $val('naturalidade') ?>">
    </div>

    <div class="col-12 mt-2"><h6 class="text-primary fw-semibold">Contato</h6></div>
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

    <div class="col-12 mt-2"><h6 class="text-primary fw-semibold">Endereço</h6></div>
    <div class="col-md-2">
        <label class="form-label">CEP</label>
        <input type="text" name="cep" class="form-control mascara-cep" value="<?= $val('cep') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Logradouro</label>
        <input type="text" name="logradouro" class="form-control" value="<?= $val('logradouro') ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Número</label>
        <input type="text" name="numero" class="form-control" value="<?= $val('numero') ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Complemento</label>
        <input type="text" name="complemento" class="form-control" value="<?= $val('complemento') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" class="form-control" value="<?= $val('bairro') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cidade</label>
        <input type="text" name="cidade" class="form-control" value="<?= $val('cidade') ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">UF</label>
        <input type="text" name="estado" class="form-control" maxlength="2" value="<?= $val('estado') ?>">
    </div>

    <div class="col-12 mt-2"><h6 class="text-primary fw-semibold">Informações Eclesiásticas</h6></div>
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
        <label class="form-label">Data de Conversão</label>
        <input type="date" name="data_conversao" class="form-control" value="<?= $val('data_conversao') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data de Batismo</label>
        <input type="date" name="data_batismo" class="form-control" value="<?= $val('data_batismo') ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Local do Batismo</label>
        <input type="text" name="local_batismo" class="form-control" value="<?= $val('local_batismo') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Igreja Anterior</label>
        <input type="text" name="igreja_anterior" class="form-control" value="<?= $val('igreja_anterior') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data de Recebimento</label>
        <input type="date" name="data_recebimento" class="form-control" value="<?= $val('data_recebimento') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Tipo de Membro</label>
        <select name="tipo_membro" class="form-select">
            <?php foreach (['Comunhão', 'Novo convertido', 'Batizando', 'Congregado', 'Visitante'] as $t): ?>
                <option value="<?= $t ?>" <?= ($registro['tipo_membro'] ?? 'Comunhão') === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Cargo</label>
        <select name="cargo" class="form-select">
            <option value="">Nenhum</option>
            <?php foreach (['Membro', 'Obreiro', 'Diácono', 'Presbítero', 'Evangelista', 'Pastor', 'Líder'] as $c): ?>
                <option value="<?= $c ?>" <?= ($registro['cargo'] ?? old('cargo')) === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <?php foreach (['Ativo', 'Inativo', 'Transferido', 'Desligado', 'Falecido'] as $s): ?>
                <option value="<?= $s ?>" <?= ($registro['status'] ?? 'Ativo') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-12">
        <label class="form-label">Observações</label>
        <textarea name="observacoes" class="form-control" rows="2"><?= $val('observacoes') ?></textarea>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
        <a href="<?= site_url('membros') ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?= $this->endSection() ?>
