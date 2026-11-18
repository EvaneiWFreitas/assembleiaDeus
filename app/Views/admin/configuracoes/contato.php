<?php
$valor = static fn (string $campo, mixed $padrao = ''): string => (string) old($campo, $valores[$campo] ?? $padrao);
$fotoPastor = (string) ($valores['pastor_foto'] ?? '');
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('contato')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver no site
    </a>
</div>

<form method="post" action="<?= e(url('admin/configuracoes/contato')) ?>" enctype="multipart/form-data" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-address-book me-2"></i>Dados de contato</h2>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="campo_cnpj" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" id="campo_cnpj" name="cnpj"
                           value="<?= e($valor('ministerio_cnpj')) ?>" maxlength="40">
                </div>
                <div class="col-lg-6">
                    <label for="campo_email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="campo_email" name="email"
                           value="<?= e($valor('ministerio_email')) ?>" placeholder="contato@igreja.com" maxlength="150">
                </div>
                <div class="col-lg-4">
                    <label for="campo_telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="campo_telefone" name="telefone"
                           value="<?= e($valor('ministerio_telefone')) ?>" placeholder="(00) 0000-0000" maxlength="40">
                </div>
                <div class="col-lg-4">
                    <label for="campo_whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="campo_whatsapp" name="whatsapp"
                           value="<?= e($valor('ministerio_whatsapp')) ?>" placeholder="(00) 00000-0000" maxlength="40">
                </div>
                <div class="col-lg-4">
                    <label for="campo_endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="campo_endereco" name="endereco"
                           value="<?= e($valor('ministerio_endereco')) ?>" placeholder="Rua, número, bairro, cidade/UF" maxlength="255">
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-user-tie me-2"></i>Pastor Presidente</h2>
            <div class="row g-3 align-items-start">
                <div class="col-lg-5">
                    <label for="campo_rotulo_pastor" class="form-label">Rótulo exibido</label>
                    <input type="text" class="form-control" id="campo_rotulo_pastor" name="rotulo_pastor"
                           value="<?= e($valor('contato_rotulo_pastor', 'Pastor Presidente')) ?>" maxlength="80">
                    <div class="form-text">Aparece antes do nome, seguido de dois pontos.</div>
                </div>
                <div class="col-lg-7">
                    <label for="campo_pastor_nome" class="form-label">Nome do Pastor Presidente</label>
                    <input type="text" class="form-control" id="campo_pastor_nome" name="pastor_nome"
                           value="<?= e($valor('pastor_presidente')) ?>" placeholder="Ex.: Pr.William Nascimento" maxlength="120">
                    <div class="form-text">Exibido como “<?= e($valor('contato_rotulo_pastor', 'Pastor Presidente')) ?>: nome” no site.</div>
                </div>
                <div class="col-lg-7">
                    <label for="campo_foto_pastor" class="form-label">Foto do pastor</label>
                    <input type="file" class="form-control" id="campo_foto_pastor" name="foto_pastor"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a foto atual.</div>
                    <?php if ($fotoPastor !== ''): ?>
                        <div class="mt-3">
                            <img src="<?= e(url($fotoPastor)) ?>" alt="Foto atual do Pastor Presidente" class="admin-upload-preview">
                            <button type="submit" form="form-remover-foto-pastor"
                                    class="btn btn-sm btn-ad-outline text-danger mt-2 d-block"
                                    onclick="return confirm('Remover a foto do Pastor Presidente?');">
                                <i class="fas fa-trash me-1"></i>Remover foto
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-pen-nib me-2"></i>Textos exibidos</h2>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="campo_eyebrow" class="form-label">Página de contato — destaque</label>
                    <input type="text" class="form-control" id="campo_eyebrow" name="eyebrow"
                           value="<?= e($valor('contato_eyebrow', 'Estamos à disposição')) ?>" maxlength="80">
                </div>
                <div class="col-lg-6">
                    <label for="campo_titulo_pagina" class="form-label">Página de contato — título</label>
                    <input type="text" class="form-control" id="campo_titulo_pagina" name="titulo_pagina"
                           value="<?= e($valor('contato_titulo', 'Fale conosco')) ?>" maxlength="120">
                </div>
                <div class="col-lg-4">
                    <label for="campo_titulo_bloco" class="form-label">Bloco de informações</label>
                    <input type="text" class="form-control" id="campo_titulo_bloco" name="titulo_bloco"
                           value="<?= e($valor('contato_bloco_titulo', 'Informações')) ?>" maxlength="120">
                </div>
                <div class="col-lg-4">
                    <label for="campo_home_eyebrow" class="form-label">Home — destaque</label>
                    <input type="text" class="form-control" id="campo_home_eyebrow" name="home_eyebrow"
                           value="<?= e($valor('contato_home_eyebrow', 'Fale conosco')) ?>" maxlength="80">
                </div>
                <div class="col-lg-4">
                    <label for="campo_home_titulo" class="form-label">Home — título</label>
                    <input type="text" class="form-control" id="campo_home_titulo" name="home_titulo"
                           value="<?= e($valor('contato_home_titulo', 'Entre em contato')) ?>" maxlength="120">
                </div>
                <div class="col-lg-6">
                    <label for="campo_home_bloco" class="form-label">Home — bloco de informações</label>
                    <input type="text" class="form-control" id="campo_home_bloco" name="home_bloco"
                           value="<?= e($valor('contato_home_bloco_titulo', 'Informações de contato')) ?>" maxlength="120">
                </div>
                <div class="col-lg-6">
                    <label for="campo_mensagem_vazia" class="form-label">Mensagem quando não há dados</label>
                    <textarea class="form-control" id="campo_mensagem_vazia" name="mensagem_vazia" rows="2" maxlength="255"><?= e($valor('contato_mensagem_vazia', 'Os dados de contato serão publicados aqui pela administração.')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar informações
        </button>
    </div>
</form>

<form id="form-remover-foto-pastor" method="post"
      action="<?= e(url('admin/configuracoes/contato/foto/remover')) ?>" class="d-none">
    <?= App\Core\Csrf::field() ?>
</form>
