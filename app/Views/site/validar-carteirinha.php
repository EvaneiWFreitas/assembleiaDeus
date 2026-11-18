<div class="ad-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ad-eyebrow">Verificação de documento</span>
            <h1 class="ad-titulo-secao">Validação de carteirinha</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($carteirinha === null): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-circle-xmark fa-2x d-block mb-2"></i>
                        <strong>Carteirinha não encontrada.</strong>
                        <p class="mb-0 small">Verifique o código informado e tente novamente.</p>
                    </div>
                <?php elseif (!$valida): ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-triangle-exclamation fa-2x d-block mb-2"></i>
                        <strong>Carteirinha vencida.</strong>
                        <p class="mb-0 small">O documento existe, mas o prazo de validade expirou.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-circle-check fa-2x d-block mb-2"></i>
                        <strong>Carteirinha válida.</strong>
                        <p class="mb-0 small">Documento conferido com sucesso nos registros do ministério.</p>
                    </div>
                <?php endif; ?>

                <?php if ($carteirinha !== null): ?>
                    <div class="ad-card p-4">
                        <h4 class="mb-3">Dados do documento</h4>
                        <div class="row g-2 small">
                            <div class="col-sm-6"><strong>Nome:</strong> <?= e($carteirinha['nome_pessoa'] ?? '—') ?></div>
                            <div class="col-sm-6"><strong>Código:</strong> <?= e($carteirinha['codigo_pessoa'] ?? '—') ?></div>
                            <div class="col-sm-6"><strong>Tipo:</strong> <?= e($carteirinha['tipo'] === 'MEMBRO' ? 'Membro' : 'Obreiro') ?></div>
                            <div class="col-sm-6"><strong>Modelo:</strong> <?= e($carteirinha['modelo']) ?></div>
                            <div class="col-sm-6"><strong>Cargo:</strong> <?= e($carteirinha['cargo'] ?? '—') ?></div>
                            <div class="col-sm-6"><strong>Congregação:</strong> <?= e($carteirinha['congregacao'] ?? '—') ?></div>
                            <div class="col-sm-6"><strong>Emissão:</strong> <?= e(dataExtenso((string) $carteirinha['data_emissao'])) ?></div>
                            <div class="col-sm-6"><strong>Validade:</strong> <?= e($carteirinha['data_validade'] ? dataExtenso((string) $carteirinha['data_validade']) : 'Não informada') ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="<?= e(url()) ?>" class="btn btn-ad-outline"><i class="fas fa-arrow-left me-2"></i>Voltar ao início</a>
                </div>
            </div>
        </div>
    </div>
</div>
