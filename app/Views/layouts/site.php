<?php

$siteNome  = $config['ministerio_nome'] ?? 'Assembleia de Deus';
$siteSigla = $config['ministerio_sigla'] ?? 'AD';
$logotipo  = trim((string) ($config['logotipo'] ?? ''));
$logoUrl   = $logotipo !== '' ? url('uploads/' . $logotipo) : url('assets/images/logo.svg');
$extensaoLogo = strtolower(pathinfo($logotipo !== '' ? $logotipo : 'logo.svg', PATHINFO_EXTENSION));
$logoMime  = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp'][$extensaoLogo] ?? 'image/png';
$corPrim   = $config['cor_primaria'] ?? '#17324d';
$corSec    = $config['cor_secundaria'] ?? '#c79a37';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($metaDescricao ?? ($config['ministerio_endereco'] ?? $siteNome)) ?>">
    <title><?= e($title ?? $siteNome) ?> | <?= e($siteNome) ?></title>

    <link rel="icon" href="<?= e($logoUrl) ?>" type="<?= e($logoMime) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body style="--ad-marinho: <?= e($corPrim) ?>; --ad-dourado: <?= e($corSec) ?>;">
<?php require ROOT_PATH . '/includes/navbar.php'; ?>
<?= App\Core\View::partial('alerts'); ?>

<main id="conteudo">
    <?= $content ?>
</main>

<?php require ROOT_PATH . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
