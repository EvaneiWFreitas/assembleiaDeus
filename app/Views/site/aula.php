<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<style>
    .texto-conteudo img { max-width: 100%; height: auto; border-radius: .375rem; }
    .texto-conteudo table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
    .texto-conteudo th, .texto-conteudo td { border: 1px solid #dee2e6; padding: .5rem; vertical-align: top; }
    .texto-conteudo th { background: #e9ecef; font-weight: 600; }
    .texto-conteudo ul, .texto-conteudo ol { margin: .5rem 0; padding-left: 1.5rem; }
    .texto-conteudo blockquote { border-left: 4px solid var(--azul); padding-left: 1rem; margin: 1rem 0; font-style: italic; color: var(--cinza); }
    .texto-conteudo pre { background: #f8f9fa; padding: 1rem; overflow-x: auto; border-radius: .375rem; }
    .texto-conteudo code { font-family: monospace; }
    .texto-conteudo hr { border-color: #dee2e6; margin: 1.5rem 0; }
</style>

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

<?php if (! empty($aula['foto'])): ?>
    <img src="<?= base_url('uploads/aulas/' . $aula['foto']) ?>" alt="<?= esc($aula['tema']) ?>" class="img-fluid rounded mb-3">
<?php endif; ?>

<?php if (! empty($aula['conteudo'])): ?>
    <div class="card border-0 shadow-sm p-4">
        <h6 class="fw-semibold mb-2"><i class="fa-solid fa-file-lines me-2 text-azul"></i>Orientação de estudo</h6>
        <div class="texto-conteudo"><?= rich_text($aula['conteudo']) ?></div>
    </div>
<?php endif; ?>

<?php if (! empty($aula['explicacao'])): ?>
    <div class="card border-0 shadow-sm p-4 mt-3">
        <h6 class="fw-semibold mb-2"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Sobre esta aula</h6>
        <div class="texto-conteudo"><?= rich_text($aula['explicacao']) ?></div>
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
