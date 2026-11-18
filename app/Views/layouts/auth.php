<?php
use App\Models\Configuracao;

$nomeMinisterio = Configuracao::get('ministerio_nome', 'Assembleia de Deus');
$sigla = Configuracao::get('ministerio_sigla', 'AD');
$logotipo = trim(Configuracao::get('logotipo', ''));
$logoUrl = $logotipo !== '' ? url('uploads/' . $logotipo) : url('assets/images/logo.svg');
$extensaoLogo = strtolower(pathinfo($logotipo !== '' ? $logotipo : 'logo.svg', PATHINFO_EXTENSION));
$logoMime = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp'][$extensaoLogo] ?? 'image/png';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Painel administrativo') ?> | <?= e($nomeMinisterio) ?></title>

    <link rel="icon" href="<?= e($logoUrl) ?>" type="<?= e($logoMime) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-auth-body">
    <div class="admin-auth-wrap">
        <a class="admin-auth-brand" href="<?= e(url()) ?>">
            <img src="<?= e($logoUrl) ?>" alt="Logotipo" width="52" height="52">
            <span>
                <strong><?= e($nomeMinisterio) ?></strong>
                <small><?= e($sigla) ?> · Painel administrativo</small>
            </span>
        </a>

        <?= App\Core\View::partial('alerts_admin') ?>

        <div class="card admin-auth-card shadow-sm border-0">
            <div class="card-body p-4 p-lg-5">
                <?= $content ?>
            </div>
        </div>

        <p class="admin-auth-voltar text-center mt-4 mb-0">
            <a href="<?= e(url('admin/login')) ?>"><i class="fas fa-arrow-left me-1"></i>Área administrativa</a>
            <span class="mx-2">·</span>
            <a href="<?= e(url()) ?>">Ir para o site</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
