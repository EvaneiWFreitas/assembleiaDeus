<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php $aluno = session()->get('aluno'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <a href="<?= site_url('site/area-aluno/curso/' . $curso['id']) ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Voltar para <?= esc($curso['nome'] ?? '') ?></a>
    <a href="<?= site_url('site/curso/sair') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Sair</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <?php if (! empty($aula['video_url'])): ?>
            <?php $embed = video_embed_url($aula['video_url']); ?>
            <?php if ($embed !== null): ?>
                <div class="ratio ratio-16x9 mb-3">
                    <iframe src="<?= esc($embed) ?>" title="<?= esc($aula['tema']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <div class="alert alert-light border text-center py-5 mb-3">
                    <i class="fa-solid fa-video fa-2x text-azul mb-2"></i>
                    <p class="mb-2">Clique no botão para abrir a videoaula.</p>
                    <a href="<?= esc($aula['video_url']) ?>" target="_blank" rel="noopener" class="btn btn-azul"><i class="fa-solid fa-play me-1"></i>Abrir videoaula</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-light border text-center py-5 mb-3">
                <i class="fa-solid fa-file-lines fa-2x text-azul mb-2"></i>
                <p class="text-muted mb-0">Esta aula é apenas com orientação de texto (sem vídeo).</p>
            </div>
        <?php endif; ?>

        <h4 class="fw-bold mb-2"><?= esc($aula['tema']) ?></h4>
        <p class="text-muted small mb-3"><i class="fa-regular fa-calendar me-1"></i><?= formatar_data($aula['data'] ?? null) ?></p>

        <?php if (! empty($aula['conteudo'])): ?>
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-semibold mb-2"><i class="fa-solid fa-file-lines me-2 text-azul"></i>Orientação de estudo</h6>
                <div class="texto-conteudo"><?= nl2br(esc($aula['conteudo'])) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <h6 class="fw-semibold mb-3">Aulas deste curso</h6>
        <div class="list-group">
            <?php foreach ($aulas as $a): ?>
                <a href="<?= site_url('site/area-aluno/aula/' . $a['id']) ?>" class="list-group-item list-group-item-action <?= (int) $a['id'] === (int) $aula['id'] ? 'active text-white' : '' ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small fw-semibold"><?= esc($a['tema']) ?></span>
                        <i class="fa-solid fa-<?= ! empty($a['video_url']) ? 'play' : 'file-lines' ?>"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
