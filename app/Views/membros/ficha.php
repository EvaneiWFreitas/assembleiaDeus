<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<?php $m = $registro; $vazio = static fn ($v) => ($v === null || $v === '') ? '-' : esc($v); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-id-card me-2 text-primary"></i>Ficha do Membro</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Imprimir</button>
        <?php if (tem_permissao('membros', 'editar')): ?>
            <a href="<?= site_url('membros/editar/' . $m['id']) ?>" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i>Editar</a>
        <?php endif; ?>
        <a href="<?= site_url('membros') ?>" class="btn btn-secondary">Voltar</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-4">
            <?php if (! empty($m['foto']) && is_file(ROOTPATH . 'public/uploads/membros/' . $m['foto'])): ?>
                <img src="<?= base_url('uploads/membros/' . $m['foto']) ?>" alt="Foto do membro"
                     class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
            <?php else: ?>
            <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.8rem;">
                <?= esc(mb_substr($m['nome'], 0, 1)) ?>
            </span>
            <?php endif; ?>
            <div>
                <h5 class="fw-bold mb-0"><?= esc($m['nome']) ?></h5>
                <div class="text-muted small">
                    <?= $vazio($m['tipo_membro']) ?> · <?= esc($congregacao) ?>
                    <span class="badge bg-success-subtle text-success ms-2"><?= esc($m['status']) ?></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <h6 class="text-primary fw-semibold border-bottom pb-1">Dados Pessoais</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5">CPF</dt><dd class="col-7"><?= $vazio($m['cpf']) ?></dd>
                    <dt class="col-5">RG</dt><dd class="col-7"><?= $vazio($m['rg']) ?></dd>
                    <dt class="col-5">Nascimento</dt><dd class="col-7"><?= formatar_data($m['data_nascimento']) ?></dd>
                    <dt class="col-5">Sexo</dt><dd class="col-7"><?= $m['sexo'] === 'M' ? 'Masculino' : ($m['sexo'] === 'F' ? 'Feminino' : '-') ?></dd>
                    <dt class="col-5">Estado civil</dt><dd class="col-7"><?= $vazio($m['estado_civil']) ?></dd>
                    <dt class="col-5">Naturalidade</dt><dd class="col-7"><?= $vazio($m['naturalidade']) ?></dd>
                </dl>
            </div>
            <div class="col-md-4">
                <h6 class="text-primary fw-semibold border-bottom pb-1">Contato & Endereço</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5">Telefone</dt><dd class="col-7"><?= $vazio($m['telefone']) ?></dd>
                    <dt class="col-5">WhatsApp</dt><dd class="col-7"><?= $vazio($m['whatsapp']) ?></dd>
                    <dt class="col-5">E-mail</dt><dd class="col-7"><?= $vazio($m['email']) ?></dd>
                    <dt class="col-5">Endereço</dt><dd class="col-7"><?= $vazio(trim(($m['logradouro'] ?? '') . ', ' . ($m['numero'] ?? ''), ', ')) ?></dd>
                    <dt class="col-5">Cidade/UF</dt><dd class="col-7"><?= $vazio(trim(($m['cidade'] ?? '') . '/' . ($m['estado'] ?? ''), '/ ')) ?></dd>
                    <dt class="col-5">CEP</dt><dd class="col-7"><?= $vazio($m['cep']) ?></dd>
                </dl>
            </div>
            <div class="col-md-4">
                <h6 class="text-primary fw-semibold border-bottom pb-1">Vida Eclesiástica</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5">Conversão</dt><dd class="col-7"><?= formatar_data($m['data_conversao']) ?></dd>
                    <dt class="col-5">Batismo</dt><dd class="col-7"><?= formatar_data($m['data_batismo']) ?><?= $m['local_batismo'] ? ' (' . esc($m['local_batismo']) . ')' : '' ?></dd>
                    <dt class="col-5">Recebimento</dt><dd class="col-7"><?= formatar_data($m['data_recebimento']) ?></dd>
                    <dt class="col-5">Igreja anterior</dt><dd class="col-7"><?= $vazio($m['igreja_anterior']) ?></dd>
                    <dt class="col-5">Cargo</dt><dd class="col-7"><?= $vazio($m['cargo']) ?></dd>
                </dl>
            </div>
            <?php if ($m['observacoes']): ?>
            <div class="col-12">
                <h6 class="text-primary fw-semibold border-bottom pb-1">Observações</h6>
                <p class="small mb-0"><?= nl2br(esc($m['observacoes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
