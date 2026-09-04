<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php $aluno = session()->get('aluno'); ?>
<?php if (session()->getFlashdata('sucesso_aluno')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('sucesso_aluno')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('erro_login')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('erro_login')) ?></div>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Olá, <?= esc($aluno['nome'] ?? 'Aluno') ?>!</h4>
        <p class="text-muted mb-0">Estes são os seus cursos. Assista às videoaulas e leia a orientação de texto.</p>
    </div>
    <a href="<?= site_url('site/curso/sair') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Sair</a>
</div>

<?php if (empty($inscricoes)): ?>
    <div class="alert alert-light border text-center py-5">
        <i class="fa-solid fa-book-open fa-2x text-azul mb-3"></i>
        <p class="mb-2">Você ainda não está matriculado em nenhum curso.</p>
        <a href="<?= site_url('site/cursos') ?>" class="btn btn-azul">Ver cursos disponíveis</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($inscricoes as $i): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-valor p-4 d-flex flex-column">
                <?php if (! empty($i['curso_foto'])): ?>
                    <img src="<?= base_url('uploads/cursos/' . $i['curso_foto']) ?>" alt="<?= esc($i['curso_nome']) ?>" class="rounded mb-3" style="width:100%;height:140px;object-fit:cover">
                <?php else: ?>
                    <div class="icone mb-3"><i class="fa-solid fa-book-bible"></i></div>
                <?php endif; ?>
                <h5 class="fw-semibold mb-1"><?= esc($i['curso_nome']) ?></h5>
                <p class="text-muted small mb-3"><?= esc($i['descricao'] ?? '') ?></p>
                <div class="d-flex gap-3 text-muted small mb-3">
                    <span><i class="fa-solid fa-video me-1 text-azul"></i><?= (int) $i['total_aulas'] ?> aulas</span>
                    <span class="badge <?= $i['status'] === 'Matriculado' ? 'bg-azul' : 'bg-secondary' ?>"><?= esc($i['status']) ?></span>
                </div>
                <a href="<?= site_url('site/area-aluno/curso/' . $i['curso_id']) ?>" class="btn btn-azul mt-auto">Acessar curso</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
