<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php
    $icones = [
        'Culto'    => 'fa-sun',
        'Reunião'  => 'fa-people-group',
        'Evento'   => 'fa-calendar-star',
        'Retiro'   => 'fa-mountain-sun',
        'Encontro' => 'fa-handshake',
        'Outro'    => 'fa-calendar-days',
    ];
    $icone = $icones[$evento['tipo']] ?? 'fa-calendar-days';

    $dias = ['Sunday'=>'Domingo','Monday'=>'Segunda-feira','Tuesday'=>'Terça-feira','Wednesday'=>'Quarta-feira','Thursday'=>'Quinta-feira','Friday'=>'Sexta-feira','Saturday'=>'Sábado'];
    $diaSemana = $dias[date('l', strtotime($evento['data_inicio']))] ?? '';

    $cores = ['Pendente'=>'warning','Confirmado'=>'success','Cancelado'=>'danger','Concluído'=>'secondary'];
    $corBadge = $cores[$evento['status']] ?? 'secondary';
?>

<div class="text-center mb-5">
    <div class="d-inline-block rounded-circle mb-3" style="width:80px;height:80px;background:<?= esc($evento['cor'] ?? 'var(--azul)') ?>;opacity:.15;"></div>
    <div class="mb-2">
        <span class="badge bg-<?= $corBadge ?>-subtle text-<?= $corBadge ?> fs-6 mb-2"><?= esc($evento['status']) ?></span>
        <span class="badge bg-info-subtle text-info fs-6 mb-2"><?= esc($evento['tipo']) ?></span>
    </div>
    <h1 class="fw-bold" style="color:var(--azul-escuro);"><?= esc($evento['titulo']) ?></h1>
</div>

<div class="row justify-content-center g-4 mb-5">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-grid place-items-center" style="width:48px;height:48px;background:var(--azul-claro);min-width:48px;">
                                <i class="fa-solid fa-calendar-days text-azul"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Data</small>
                                <strong><?= $diaSemana ?>, <?= date('d/m/Y', strtotime($evento['data_inicio'])) ?></strong>
                                <?php if (!empty($evento['data_fim']) && $evento['data_fim'] !== $evento['data_inicio']): ?>
                                    <br><small class="text-muted">até <?= date('d/m/Y', strtotime($evento['data_fim'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-grid place-items-center" style="width:48px;height:48px;background:var(--azul-claro);min-width:48px;">
                                <i class="fa-solid fa-clock text-azul"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Horário</small>
                                <strong><?= $evento['hora_inicio'] ? esc(substr($evento['hora_inicio'], 0, 5)) . ($evento['hora_fim'] ? ' às ' . esc(substr($evento['hora_fim'], 0, 5)) : '') : 'A definir' ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($evento['local'])): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-grid place-items-center" style="width:48px;height:48px;background:var(--azul-claro);min-width:48px;">
                                <i class="fa-solid fa-location-dot text-azul"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Local</small>
                                <strong><?= esc($evento['local']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($evento['responsavel'])): ?>
                    <?php $fotoResp = $evento['responsavel_foto_url'] ?? null; ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($fotoResp): ?>
                                <img src="<?= esc($fotoResp) ?>" alt="Foto de <?= esc($evento['responsavel']) ?>"
                                     class="rounded-circle" style="width:52px;height:52px;object-fit:cover;min-width:52px;">
                            <?php else: ?>
                                <div class="rounded-circle d-grid place-items-center text-white" style="width:52px;height:52px;min-width:52px;background:var(--azul);">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <small class="text-muted d-block">Responsável</small>
                                <strong><?= esc($evento['responsavel']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($evento['congregacao_nome'])): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-grid place-items-center" style="width:48px;height:48px;background:var(--azul-claro);min-width:48px;">
                                <i class="fa-solid fa-place-of-worship text-azul"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Congregação</small>
                                <strong><?= esc($evento['congregacao_nome']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($evento['recorrencia'])): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-grid place-items-center" style="width:48px;height:48px;background:var(--azul-claro);min-width:48px;">
                                <i class="fa-solid fa-rotate text-azul"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Recorrência</small>
                                <strong><?= ucfirst($evento['recorrencia']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($evento['descricao'])): ?>
                <hr class="my-4">
                <h5 class="fw-semibold mb-3" style="color:var(--azul-escuro);">Sobre o evento</h5>
                <p class="text-muted" style="white-space:pre-line;"><?= esc($evento['descricao']) ?></p>
                <?php endif; ?>

                <?php if (!empty($evento['observacoes'])): ?>
                <hr class="my-4">
                <h5 class="fw-semibold mb-3" style="color:var(--azul-escuro);">Observações</h5>
                <p class="text-muted" style="white-space:pre-line;"><?= esc($evento['observacoes']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <i class="fa-solid fa-hand-point-right fa-2x mb-3" style="color:var(--dourado);"></i>
                <h5 class="fw-semibold" style="color:var(--azul-escuro);">Confirmar Presença</h5>
                <p class="text-muted small">Sua presença é muito importante. Confirme abaixo!</p>

                <?php if (session()->getFlashdata('sucesso_presenca')): ?>
                    <div class="alert alert-success py-2 small"><?= esc(session()->getFlashdata('sucesso_presenca')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('erro_presenca')): ?>
                    <div class="alert alert-danger py-2 small"><?= esc(session()->getFlashdata('erro_presenca')) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('site/evento/' . $evento['id'] . '/confirmar') ?>" class="text-start">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <input type="text" name="nome" class="form-control" placeholder="Seu nome" required minlength="2" value="<?= esc(old('nome')) ?>">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="telefone" class="form-control" placeholder="Telefone / WhatsApp (opcional)" value="<?= esc(old('telefone')) ?>">
                    </div>
                    <button class="btn btn-azul w-100 mb-2">
                        <i class="fa-solid fa-check me-2"></i>Confirmar presença
                    </button>
                </form>

                <?php if (!empty($igreja['whatsapp'])): ?>
                    <a href="https://wa.me/55<?= preg_replace('/\D/', '', (string) $igreja['whatsapp']) ?>?text=<?= urlencode('Olá! Gostaria de confirmar minha presença no evento: ' . $evento['titulo']) ?>"
                       target="_blank" rel="noopener" class="btn btn-outline-azul w-100 btn-sm">
                        <i class="fa-brands fa-whatsapp me-2"></i>Confirmar via WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <a href="<?= site_url() ?>#horarios" class="btn btn-outline-azul w-100">
            <i class="fa-solid fa-arrow-left me-2"></i>Voltar à página inicial
        </a>
    </div>
</div>

<?= $this->endSection() ?>