<?php
/**
 * Layout principal — recebe: $titulo, $conteudo (view interna), $usuario, $permissoes.
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Sistema de Gestão de Igrejas') ?> | Gestão de Igrejas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body>
<?= view('layouts/navbar', ['usuario' => $usuario ?? []]) ?>

<div class="d-flex">
    <?= view('layouts/sidebar', ['permissoes' => $permissoes ?? []]) ?>

    <main class="flex-grow-1 app-conteudo">
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('sucesso')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= esc(session()->getFlashdata('erro')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="p-3 p-lg-4">
            <?= $this->renderSection('conteudo') ?>
        </div>

        <?= view('layouts/footer') ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
