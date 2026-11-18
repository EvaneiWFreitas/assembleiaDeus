<?php
$rotuloPastor = trim((string) ($config['contato_rotulo_pastor'] ?? 'Pastor Presidente'));
$pastorNome = trim((string) ($config['pastor_presidente'] ?? ''));
$contatoCnpj = trim((string) ($config['ministerio_cnpj'] ?? ''));
$contatoEndereco = trim((string) ($config['ministerio_endereco'] ?? ''));
$contatoTelefone = trim((string) ($config['ministerio_telefone'] ?? ''));
$contatoWhatsapp = trim((string) ($config['ministerio_whatsapp'] ?? ''));
$contatoEmail = trim((string) ($config['ministerio_email'] ?? ''));
$mensagemContatoVazia = trim((string) ($config['contato_mensagem_vazia'] ?? 'Os dados de contato serão publicados aqui pela administração.'));
$temContato = $contatoCnpj !== '' || $contatoEndereco !== '' || $contatoTelefone !== ''
    || $contatoWhatsapp !== '' || $contatoEmail !== '' || $pastorNome !== '';
?>

<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contato</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="ad-eyebrow"><?= e((string) ($config['contato_eyebrow'] ?? 'Estamos à disposição')) ?></span>
            <h1 class="ad-titulo-secao"><?= e((string) ($config['contato_titulo'] ?? 'Fale conosco')) ?></h1>
        </div>

        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="ad-card h-100">
                    <h4><?= e((string) ($config['contato_bloco_titulo'] ?? 'Informações')) ?></h4>
                    <ul class="list-unstyled ad-lista-contato">
                        <?php if ($contatoCnpj !== ''): ?>
                            <li><i class="fas fa-id-card"></i><span><?= e($contatoCnpj) ?></span></li>
                        <?php endif; ?>
                        <?php if ($contatoEndereco !== ''): ?>
                            <li><i class="fas fa-map-marker-alt"></i><span><?= e($contatoEndereco) ?></span></li>
                        <?php endif; ?>
                        <?php if ($contatoTelefone !== ''): ?>
                            <li><i class="fas fa-phone"></i><span><?= e($contatoTelefone) ?></span></li>
                        <?php endif; ?>
                        <?php if ($contatoWhatsapp !== ''): ?>
                            <li><i class="fab fa-whatsapp"></i><span><?= e($contatoWhatsapp) ?></span></li>
                        <?php endif; ?>
                        <?php if ($contatoEmail !== ''): ?>
                            <li><i class="fas fa-envelope"></i><span><?= e($contatoEmail) ?></span></li>
                        <?php endif; ?>
                        <?php if ($pastorNome !== ''): ?>
                            <li><i class="fas fa-user-tie"></i><span><?= e($rotuloPastor) ?>: <?= e($pastorNome) ?></span></li>
                        <?php endif; ?>
                    </ul>
                    <?php if (!$temContato && $mensagemContatoVazia !== ''): ?>
                        <p class="small text-muted mb-0"><?= e($mensagemContatoVazia) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ad-card">
                    <?= App\Core\View::partial('form_contato'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
