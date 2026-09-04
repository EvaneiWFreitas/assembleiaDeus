<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-eye me-2 text-primary"></i><?= esc($titulo) ?></h4>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= site_url('documentos/imprimir/' . $registro['id']) ?>" class="btn btn-success" target="_blank"><i class="fa-solid fa-print me-1"></i>Imprimir Oficial</a>
        <a href="<?= site_url('documentos') ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-info-subtle text-info me-2"><?= esc($tipos[$registro['tipo']] ?? $registro['tipo']) ?></span>
            <span class="fw-semibold"><?= esc($registro['titulo']) ?></span>
        </div>
        <div class="text-muted small">
            <?= $registro['padrao'] ? '<span class="badge bg-success-subtle text-success"><i class="fa-solid fa-star me-1"></i>Padrão</span>' : '' ?>
            <?= badge_status($registro['ativo']) ?>
        </div>
    </div>
    <div class="card-body">
        <?php $ehCertificado = str_starts_with($registro['tipo'] ?? '', 'certificado'); ?>
        <div class="documento-preview-wrapper<?= $ehCertificado ? ' paisagem' : '' ?>">
            <div class="border rounded p-4 bg-white <?= $ehCertificado ? 'documento-paisagem' : '' ?>" id="previewDocumento" style="min-height: 300px;">
                <?= $registro['conteudo'] ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($variaveis)): ?>
<?php $tipoCampo = fn(string $v): string => str_starts_with($v, 'hora') ? 'time' : (str_starts_with($v, 'data') ? 'date' : 'text'); ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light fw-semibold">
        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Dados para preencher o documento
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">Digite os dados nos campos abaixo e clique em <strong>Aplicar</strong> para preenchê-los no documento acima.</p>
        <form id="formTeste" class="row g-3">
            <?php foreach ($variaveis as $v): ?>
                <div class="col-md-4">
                    <label class="form-label small">{<?= esc($v) ?>}</label>
                    <input <?= $tipoCampo($v) === 'text' ? 'type="text"' : 'type="' . $tipoCampo($v) . '"' ?> name="<?= esc($v) ?>" class="form-control form-control-sm" data-var="<?= esc($v) ?>" placeholder="Digite <?= esc($v) ?>">
                </div>
            <?php endforeach; ?>
            <div class="col-12 d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="btnAplicarTeste"><i class="fa-solid fa-magic me-1"></i>Aplicar no Documento</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimparTeste"><i class="fa-solid fa-eraser me-1"></i>Limpar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($variaveis)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light fw-semibold">
        <i class="fa-solid fa-code me-2 text-muted"></i>Variáveis deste Modelo
    </div>
    <div class="card-body">
        <div class="row g-2">
            <?php foreach ($variaveis as $v): ?>
                <div class="col-md-4 col-lg-3">
                    <code class="bg-white px-2 py-1 border rounded d-block text-break">{<?= esc($v) ?>}</code>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script {csp-script-nonce}>
const conteudoOriginalDoModelo = <?= json_encode($registro['conteudo']) ?>;
const preview = document.getElementById('previewDocumento');

document.getElementById('btnAplicarTeste')?.addEventListener('click', function() {
    if (!preview) return;
    const form = document.getElementById('formTeste');
    const formData = new FormData(form);
    let html = conteudoOriginalDoModelo;
    for (const [key, value] of formData.entries()) {
        const valor = value || '{' + key + '}';
        html = html.replace(new RegExp('\\{' + key + '\\}', 'g'), () => valor);
    }
    preview.innerHTML = html;
});

document.getElementById('btnLimparTeste')?.addEventListener('click', function() {
    const form = document.getElementById('formTeste');
    form.reset();
    if (preview) {
        preview.innerHTML = conteudoOriginalDoModelo;
    }
});
</script>
<?= $this->endSection() ?>
