<?php
$anoAtual = date('Y');
$redes    = \App\Models\ConteudoPublico::redesSociais();
?>
<footer class="ad-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?= e($logoUrl) ?>" alt="Logotipo" height="52" width="52">
                    <div>
                        <h5 class="mb-0"><?= e($siteNome) ?></h5>
                        <small class="ad-footer-sigla"><?= e($siteSigla) ?></small>
                    </div>
                </div>
                <p class="small mb-0">
                    Igreja Matriz, congregações e pontos de pregação unidos na missão de anunciar o evangelho
                    e cuidar das famílias.
                </p>
            </div>

            <div class="col-lg-2">
                <h6 class="ad-footer-titulo">Navegação</h6>
                <ul class="list-unstyled small mb-0 ad-footer-links">
                    <li><a href="<?= e(url('historia')) ?>">Nossa história</a></li>
                    <li><a href="<?= e(url('missao-visao-valores')) ?>">Missão e visão</a></li>
                    <li><a href="<?= e(url('igrejas')) ?>">Igreja Matriz</a></li>
                    <li><a href="<?= e(url('congregacoes')) ?>">Congregações</a></li>
                    <li><a href="<?= e(url('eventos')) ?>">Eventos</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="ad-footer-titulo">Contato</h6>
                <ul class="list-unstyled small mb-0 ad-footer-links">
                    <?php if (($config['ministerio_endereco'] ?? '') !== ''): ?>
                        <li><i class="fas fa-location-dot me-2"></i><?= e($config['ministerio_endereco']) ?></li>
                    <?php endif; ?>
                    <?php if (($config['ministerio_telefone'] ?? '') !== ''): ?>
                        <li><i class="fas fa-phone me-2"></i><?= e($config['ministerio_telefone']) ?></li>
                    <?php endif; ?>
                    <?php if (($config['ministerio_email'] ?? '') !== ''): ?>
                        <li><i class="fas fa-envelope me-2"></i><?= e($config['ministerio_email']) ?></li>
                    <?php endif; ?>
                    <?php if (($config['pastor_presidente'] ?? '') !== ''): ?>
                        <li><i class="fas fa-user-tie me-2"></i>Pastor Presidente: <?= e($config['pastor_presidente']) ?></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="ad-footer-titulo">Redes sociais</h6>
                <div class="ad-footer-redes mb-3">
                    <?php foreach ($redes as $rede): ?>
                        <a href="<?= e($rede['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($rede['nome']) ?>">
                            <i class="<?= e($rede['icone']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="small mb-0">
                    Seus dados são tratados conforme a LGPD.
                    <a href="<?= e(url('contato')) ?>" class="ad-footer-link">Fale conosco</a>
                </p>
            </div>
        </div>
    </div>

    <div class="ad-footer-barra">
        <div class="container d-flex flex-wrap justify-content-between gap-2 py-3 small">
            <span>&copy; <?= (int) $anoAtual ?> <?= e($siteNome) ?>. Todos os direitos reservados.</span>
            <span>Sistema assembleiaDeus — Gestão Administrativa e Eclesiástica</span>
        </div>
    </div>
</footer>
