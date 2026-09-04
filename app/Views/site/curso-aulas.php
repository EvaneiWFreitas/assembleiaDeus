<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php $aluno = session()->get('aluno'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><?= esc($curso['nome'] ?? 'Curso') ?></h4>
        <p class="text-muted mb-0">Escolha uma aula para assistir.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('site/area-aluno') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Minha área</a>
        <a href="<?= site_url('site/curso/sair') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Sair</a>
    </div>
</div>

<?php if (empty($aulas)): ?>
    <div class="alert alert-light border text-center py-5">
        <i class="fa-solid fa-video fa-2x text-azul mb-3"></i>
        <p class="mb-0 text-muted">As aulas deste curso ainda serão disponibilizadas. Volte em breve!</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($aulas as $a): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-valor p-4 h-100">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-azul"><i class="fa-solid fa-book-open me-1"></i>Aula</span>
                    <span class="small text-muted"><?= formatar_data($a['data'] ?? null) ?></span>
                </div>
                <h6 class="fw-semibold"><?= esc($a['tema']) ?></h6>
                <?php if (! empty($a['foto'])): ?>
                    <img src="<?= base_url('uploads/aulas/' . $a['foto']) ?>" alt="<?= esc($a['tema']) ?>" class="img-fluid rounded mb-2" style="max-height:120px;object-fit:cover;width:100%">
                <?php endif; ?>
                <?php if (! empty($a['conteudo'])): ?>
                    <p class="text-muted small mb-3"><?= esc(trim(strip_tags($a['conteudo']))) ?></p>
                <?php endif; ?>
                <div class="mt-auto pt-2">
                    <?php if (! empty($a['video_url'])): ?>
                        <a class="btn btn-azul btn-sm w-100" href="<?= site_url('site/area-aluno/aula/' . $a['id']) ?>"><i class="fa-solid fa-play me-1"></i>Assistir videoaula</a>
                    <?php elseif (! empty($a['conteudo'])): ?>
                        <a class="btn btn-outline-secondary btn-sm w-100" href="<?= site_url('site/area-aluno/aula/' . $a['id']) ?>"><i class="fa-solid fa-file-lines me-1"></i>Ler orientação</a>
                    <?php else: ?>
                        <a class="btn btn-outline-secondary btn-sm w-100" href="<?= site_url('site/area-aluno/aula/' . $a['id']) ?>">Ver aula</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
