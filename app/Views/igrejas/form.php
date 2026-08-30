<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<h4 class="fw-bold mb-3"><i class="fa-solid fa-church me-2 text-primary"></i>Dados da Igreja</h4>

<?php if (isset($erros)) foreach ($erros as $e): ?>
    <div class="alert alert-warning py-2"><?= esc($e) ?></div>
<?php endforeach ?>

<form method="post" action="<?= site_url('igreja') ?>" enctype="multipart/form-data" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-12"><h6 class="text-primary fw-semibold">Identificação</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nome da Igreja <span class="text-danger">*</span></label>
        <input type="text" name="nome" class="form-control" required value="<?= esc($registro['nome']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Razão Social</label>
        <input type="text" name="razao_social" class="form-control" value="<?= esc($registro['razao_social']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">CNPJ</label>
        <input type="text" name="cnpj" class="form-control mascara-cnpj" placeholder="00.000.000/0000-00" value="<?= esc($registro['cnpj']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Data de Fundação</label>
        <input type="date" name="data_fundacao" class="form-control" value="<?= esc($registro['data_fundacao']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Pastor Responsável</label>
        <input type="text" name="pastor_responsavel" class="form-control" value="<?= esc($registro['pastor_responsavel']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Logo (PNG/JPG/WebP até 2MB)</label>
        <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.webp">
    </div>

    <div class="col-12 mt-4"><h6 class="text-primary fw-semibold">Contato</h6></div>
    <div class="col-md-3">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control mascara-telefone" value="<?= esc($registro['telefone']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control mascara-telefone" value="<?= esc($registro['whatsapp']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="<?= esc($registro['email']) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Site</label>
        <input type="url" name="site" class="form-control" value="<?= esc($registro['site']) ?>">
    </div>

    <div class="col-12 mt-4"><h6 class="text-primary fw-semibold">Endereço</h6></div>
    <div class="col-md-2">
        <label class="form-label">CEP</label>
        <input type="text" name="cep" class="form-control mascara-cep" value="<?= esc($registro['cep']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Logradouro</label>
        <input type="text" name="logradouro" class="form-control" value="<?= esc($registro['logradouro']) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Número</label>
        <input type="text" name="numero" class="form-control" value="<?= esc($registro['numero']) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Complemento</label>
        <input type="text" name="complemento" class="form-control" value="<?= esc($registro['complemento']) ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" class="form-control" value="<?= esc($registro['bairro']) ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cidade</label>
        <input type="text" name="cidade" class="form-control" value="<?= esc($registro['cidade']) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Estado (UF)</label>
        <input type="text" name="estado" class="form-control" maxlength="2" value="<?= esc($registro['estado']) ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">País</label>
        <input type="text" name="pais" class="form-control" value="<?= esc($registro['pais']) ?>">
    </div>

    <div class="col-12 mt-4 d-flex gap-2">
        <?php if (tem_permissao('igreja', 'editar')): ?>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Salvar</button>
        <?php endif; ?>
    </div>
</form>

<?= $this->endSection() ?>
