<?php
$rotaAtual = trim((string) ($_GET['route'] ?? ''), '/');

$itensMenu = [
    ''         => 'Início',
    'historia' => 'História',
    'eventos'  => 'Eventos',
    'contato'  => 'Contato',
];
?>
<nav class="navbar navbar-expand-lg ad-navbar sticky-top" aria-label="Navegação principal">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= e(url()) ?>">
            <img src="<?= e($logoUrl) ?>" alt="Logotipo" height="44" width="44">
            <span class="ad-brand-texto">
                <strong><?= e($siteNome) ?></strong>
                <small><?= e($siteSigla) ?></small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublico"
                aria-controls="menuPublico" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPublico">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php foreach ($itensMenu as $rota => $rotulo): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $rotaAtual === $rota ? 'active' : '' ?>"
                           href="<?= e(url($rota)) ?>"><?= e($rotulo) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-ad-outline btn-sm" href="<?= e(url('admin/login')) ?>">
                        <i class="fas fa-lock me-1"></i>Área administrativa
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
