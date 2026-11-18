<div class="ad-section">
    <div class="container">
        <nav aria-label="Navegação estrutural">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= e(url()) ?>">Início</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contato</li>
            </ol>
        </nav>

        <div class="text-center mb-5">
            <span class="ad-eyebrow">Estamos à disposição</span>
            <h1 class="ad-titulo-secao">Fale conosco</h1>
        </div>

        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="ad-card h-100">
                    <h4>Informações</h4>
                    <ul class="list-unstyled ad-lista-contato">
                        <?php if (($config['ministerio_endereco'] ?? '') !== ''): ?>
                            <li><i class="fas fa-map-marker-alt"></i><span><?= e($config['ministerio_endereco']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_telefone'] ?? '') !== ''): ?>
                            <li><i class="fas fa-phone"></i><span><?= e($config['ministerio_telefone']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_whatsapp'] ?? '') !== ''): ?>
                            <li><i class="fab fa-whatsapp"></i><span><?= e($config['ministerio_whatsapp']) ?></span></li>
                        <?php endif; ?>
                        <?php if (($config['ministerio_email'] ?? '') !== ''): ?>
                            <li><i class="fas fa-envelope"></i><span><?= e($config['ministerio_email']) ?></span></li>
                        <?php endif; ?>
                    </ul>
                    <?php if (($config['ministerio_endereco'] ?? '') === ''): ?>
                        <p class="small text-muted mb-0">Os dados de contato serão publicados aqui pela administração.</p>
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
