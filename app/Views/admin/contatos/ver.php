<?php
$assunto = (string) ($contato['assunto'] ?? '');
$assuntoResposta = 'Re: ' . ($assunto !== '' ? $assunto : 'Contato pelo site');
$corpoResposta = 'Olá, ' . (string) ($contato['nome'] ?? '') . ",\n\n"
    . 'Agradecemos seu contato.' . "\n\n"
    . 'Mensagem original:' . "\n" . (string) ($contato['mensagem'] ?? '') . "\n\n"
    . 'Atenciosamente,' . "\n" . (string) (config('ministerio_nome') ?? '');
$mailto = 'mailto:' . (string) ($contato['email'] ?? '')
    . '?subject=' . rawurlencode($assuntoResposta)
    . '&body=' . rawurlencode($corpoResposta);
$lido = (int) ($contato['lido'] ?? 0) === 1;
$respondido = (int) ($contato['respondido'] ?? 0) === 1;
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/contatos')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar às mensagens
    </a>
</div>

<div class="card admin-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="h5 mb-1"><?= e((string) ($contato['nome'] ?? '')) ?></h2>
                <div class="small text-muted">
                    <?= e((string) ($contato['email'] ?? '')) ?>
                    <?php if ((string) ($contato['telefone'] ?? '') !== ''): ?>
                        · <?= e((string) $contato['telefone']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-1">
                <span class="badge <?= $lido ? 'text-bg-secondary' : 'text-bg-primary' ?>">
                    <?= $lido ? 'Lida' : 'Nova' ?>
                </span>
                <span class="badge <?= $respondido ? 'text-bg-success' : 'text-bg-warning' ?>">
                    <?= $respondido ? 'Respondida' : 'Pendente' ?>
                </span>
            </div>
        </div>

        <dl class="row small mb-4">
            <dt class="col-sm-3 text-muted">Assunto</dt>
            <dd class="col-sm-9"><?= e($assunto !== '' ? $assunto : 'Contato pelo site') ?></dd>
            <dt class="col-sm-3 text-muted">Recebida em</dt>
            <dd class="col-sm-9"><?= date('d/m/Y H:i:s', strtotime((string) $contato['created_at'])) ?></dd>
            <?php if ((string) ($contato['ip'] ?? '') !== ''): ?>
                <dt class="col-sm-3 text-muted">IP de origem</dt>
                <dd class="col-sm-9"><?= e((string) $contato['ip']) ?></dd>
            <?php endif; ?>
            <?php if ($respondido && !empty($contato['respondido_em'])): ?>
                <dt class="col-sm-3 text-muted">Respondida em</dt>
                <dd class="col-sm-9"><?= date('d/m/Y H:i:s', strtotime((string) $contato['respondido_em'])) ?></dd>
            <?php endif; ?>
        </dl>

        <div class="admin-mensagem-contato p-3 rounded bg-light">
            <?= nl2br(e((string) ($contato['mensagem'] ?? ''))) ?>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <a href="<?= e($mailto) ?>" class="btn btn-sm btn-ad-primary">
        <i class="fas fa-envelope me-1"></i>Responder por e-mail
    </a>
    <?php if ($podeResponder && !$respondido): ?>
        <form method="post" action="<?= e(url('admin/contatos/' . (int) $contato['id'] . '/respondido')) ?>">
            <?= App\Core\Csrf::field() ?>
            <button type="submit" class="btn btn-sm btn-ad-outline">
                <i class="fas fa-check me-1"></i>Marcar como respondida
            </button>
        </form>
    <?php endif; ?>
    <?php if ($podeExcluir): ?>
        <form method="post" action="<?= e(url('admin/contatos/' . (int) $contato['id'] . '/excluir')) ?>"
              onsubmit="return confirm('Excluir esta mensagem?');">
            <?= App\Core\Csrf::field() ?>
            <button type="submit" class="btn btn-sm btn-ad-outline text-danger">
                <i class="fas fa-trash me-1"></i>Excluir mensagem
            </button>
        </form>
    <?php endif; ?>
</div>
