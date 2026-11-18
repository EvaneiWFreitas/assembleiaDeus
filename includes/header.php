<?php
$redes = \App\Models\ConteudoPublico::redesSociais();
?>
<header class="ad-topbar">
    <div class="container">
        <div class="row align-items-center gy-1">
            <div class="col-md-7 ad-topbar-contatos">
                <?php if (($config['ministerio_telefone'] ?? '') !== ''): ?>
                    <span><i class="fas fa-phone-volume me-1"></i><?= e($config['ministerio_telefone']) ?></span>
                <?php endif; ?>
                <?php if (($config['ministerio_email'] ?? '') !== ''): ?>
                    <span class="ms-md-3"><i class="fas fa-envelope me-1"></i><?= e($config['ministerio_email']) ?></span>
                <?php endif; ?>
                <?php if (($config['ministerio_endereco'] ?? '') !== ''): ?>
                    <span class="ms-md-3"><i class="fas fa-location-dot me-1"></i><?= e($config['ministerio_endereco']) ?></span>
                <?php endif; ?>
            </div>
            <div class="col-md-5 text-md-end ad-topbar-redes">
                <?php foreach ($redes as $rede): ?>
                    <a href="<?= e($rede['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($rede['nome']) ?>">
                        <i class="<?= e($rede['icone']) ?>"></i>
                    </a>
                <?php endforeach; ?>
                <a href="<?= e(url('contato')) ?>" class="ms-2 small">Fale conosco</a>
            </div>
        </div>
    </div>
</header>
