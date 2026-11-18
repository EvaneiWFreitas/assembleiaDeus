<?php
$mensagens = App\Core\Flash::consume();
?>
<?php if ($mensagens !== []): ?>
    <div class="container ad-alertas">
        <?php foreach ($mensagens as $mensagem): ?>
            <div class="alert alert-<?= e($mensagem['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-circle-info me-2"></i><?= e($mensagem['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
